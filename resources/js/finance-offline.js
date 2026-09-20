(function () {
    const DB_NAME = 'sim-mmu-offline';
    const DB_VERSION = 6;
    const STORE_NAME = 'transaction_queue';

    function openDatabase() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (event) {
                const db = event.target.result;

                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    const store = db.createObjectStore(STORE_NAME, {
                        keyPath: 'sync_id',
                    });

                    store.createIndex('status', 'status', {
                        unique: false,
                    });

                    store.createIndex('created_at', 'created_at', {
                        unique: false,
                    });
                }
            };

            request.onsuccess = function () {
                resolve(request.result);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function addToQueue(data) {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                STORE_NAME,
                'readwrite'
            );

            const store = transaction.objectStore(STORE_NAME);

            const request = store.add(data);

            request.onsuccess = function () {
                resolve(data);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function getPendingTransactions() {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                STORE_NAME,
                'readonly'
            );

            const store = transaction.objectStore(STORE_NAME);

            const request = store
                .index('status')
                .getAll('pending_sync');

            request.onsuccess = function () {
                resolve(request.result);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function getPendingCount() {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                STORE_NAME,
                'readonly'
            );

            const store = transaction.objectStore(STORE_NAME);

            const request = store
                .index('status')
                .count('pending_sync');

            request.onsuccess = function () {
                resolve(request.result);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function updateQueueStatus(syncId, status) {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                STORE_NAME,
                'readwrite'
            );

            const store = transaction.objectStore(STORE_NAME);

            const request = store.get(syncId);

            request.onsuccess = function () {
                const item = request.result;

                if (!item) {
                    reject(new Error('Transaksi offline tidak ditemukan.'));
                    return;
                }

                item.status = status;

                const updateRequest = store.put(item);

                updateRequest.onsuccess = function () {
                    resolve(item);
                };

                updateRequest.onerror = function () {
                    reject(updateRequest.error);
                };
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function getAllTransactions() {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                STORE_NAME,
                'readonly'
            );

            const store = transaction.objectStore(STORE_NAME);

            const request = store.getAll();

            request.onsuccess = function () {
                resolve(request.result);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function markAsSynced(syncId) {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                STORE_NAME,
                'readwrite'
            );

            const store = transaction.objectStore(STORE_NAME);

            const request = store.get(syncId);

            request.onsuccess = function () {
                const item = request.result;

                if (!item) {
                    reject(new Error('Transaksi offline tidak ditemukan.'));
                    return;
                }

                item.status = 'synced';

                const updateRequest = store.put(item);

                updateRequest.onsuccess = function () {
                    resolve(item);
                };

                updateRequest.onerror = function () {
                    reject(updateRequest.error);
                };
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function syncPendingTransactions() {
        const pendingTransactions = await getPendingTransactions();

        const results = [];

        for (const item of pendingTransactions) {
            try {
                const response = await fetch('/api/offline/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        sync_id: item.sync_id,
                        transaction_kind: item.transaction_kind,
                        organization_id: item.organization_id,
                        transaction_date: item.transaction_date,
                        type: item.type,
                        amount: item.amount,
                        payment_method: item.payment_method,
                        category: item.category,
                        description: item.description ?? null,
                    }),
                });

                const data = await response.json();

                if (
                    response.ok &&
                    data.success === true &&
                    data.ack &&
                    ['acknowledged', 'duplicate'].includes(data.ack.status) &&
                    data.ack.sync_id === item.sync_id
                ) {
                    await markAsSynced(item.sync_id);

                    results.push({
                        sync_id: item.sync_id,
                        status: 'synced',
                    });
                } else {
                    results.push({
                        sync_id: item.sync_id,
                        status: 'pending_sync',
                        error: data.message || 'Sinkronisasi gagal.',
                    });
                }
            } catch (error) {
                results.push({
                    sync_id: item.sync_id,
                    status: 'pending_sync',
                    error: error.message,
                });
            }
        }

        return results;
    }

    function setupAutoSync() {
        let syncing = false;

        async function runSync() {
            if (syncing) {
                return;
            }

            if (!navigator.onLine) {
                return;
            }

            syncing = true;

            try {
                const pendingCount = await getPendingCount();

                if (pendingCount === 0) {
                    return;
                }

                const results = await syncPendingTransactions();

                console.log(
                    '[SIM-MMU Offline] Sinkronisasi selesai:',
                    results
                );
            } catch (error) {
                console.error(
                    '[SIM-MMU Offline] Sinkronisasi gagal:',
                    error
                );
            } finally {
                syncing = false;
            }
        }

        window.addEventListener('online', runSync);

        if (navigator.onLine) {
            runSync();
        }

        return runSync;
    }

    window.SimMmuOffline = {
        openDatabase,
        addToQueue,
        getPendingTransactions,
        getPendingCount,
        updateQueueStatus,
        getAllTransactions,
        markAsSynced,
        syncPendingTransactions,
        setupAutoSync,
    };

    SimMmuOffline.setupAutoSync();
})();
