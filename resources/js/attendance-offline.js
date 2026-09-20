(function () {
    const DB_NAME = 'sim-mmu-offline';
    const DB_VERSION = 6;

    const ATTENDANCE_CACHE_STORE = 'attendance_cache';
    const ATTENDANCE_QUEUE_STORE = 'attendance_queue';

    function openDatabase() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (event) {
                const db = event.target.result;

                /*
                 * Store existing:
                 * transaction_queue
                 *
                 * Jangan dihapus atau diubah.
                 */

                if (!db.objectStoreNames.contains(ATTENDANCE_CACHE_STORE)) {
                    const cacheStore = db.createObjectStore(
                        ATTENDANCE_CACHE_STORE,
                        {
                            keyPath: 'assignment_id',
                        }
                    );

                    cacheStore.createIndex(
                        'updated_at',
                        'updated_at',
                        {
                            unique: false,
                        }
                    );
                }

                if (!db.objectStoreNames.contains(ATTENDANCE_QUEUE_STORE)) {
                    const queueStore = db.createObjectStore(
                        ATTENDANCE_QUEUE_STORE,
                        {
                            keyPath: 'sync_id',
                        }
                    );

                    queueStore.createIndex(
                        'status',
                        'status',
                        {
                            unique: false,
                        }
                    );

                    queueStore.createIndex(
                        'created_at',
                        'created_at',
                        {
                            unique: false,
                        }
                    );

                    queueStore.createIndex(
                        'operation',
                        'operation',
                        {
                            unique: false,
                        }
                    );

                    queueStore.createIndex(
                        'attendance_id',
                        'attendance_id',
                        {
                            unique: false,
                        }
                    );
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

    /*
    |--------------------------------------------------------------------------
    | Cache Teaching Assignment + Students
    |--------------------------------------------------------------------------
    */

    async function cacheAssignment(data) {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                ATTENDANCE_CACHE_STORE,
                'readwrite'
            );

            const store = transaction.objectStore(
                ATTENDANCE_CACHE_STORE
            );

            const request = store.put({
                ...data,
                updated_at: new Date().toISOString(),
            });

            request.onsuccess = function () {
                resolve(data);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function getCachedAssignment(assignmentId) {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                ATTENDANCE_CACHE_STORE,
                'readonly'
            );

            const store = transaction.objectStore(
                ATTENDANCE_CACHE_STORE
            );

            const request = store.get(Number(assignmentId));

            request.onsuccess = function () {
                resolve(request.result ?? null);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function getAllCachedAssignments() {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                ATTENDANCE_CACHE_STORE,
                'readonly'
            );

            const store = transaction.objectStore(
                ATTENDANCE_CACHE_STORE
            );

            const request = store.getAll();

            request.onsuccess = function () {
                resolve(request.result);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Queue
    |--------------------------------------------------------------------------
    */

    async function addToQueue(data) {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                ATTENDANCE_QUEUE_STORE,
                'readwrite'
            );

            const store = transaction.objectStore(
                ATTENDANCE_QUEUE_STORE
            );

            const request = store.add(data);

            request.onsuccess = function () {
                resolve(data);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function getPendingAttendance() {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                ATTENDANCE_QUEUE_STORE,
                'readonly'
            );

            const store = transaction.objectStore(
                ATTENDANCE_QUEUE_STORE
            );

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
                ATTENDANCE_QUEUE_STORE,
                'readonly'
            );

            const store = transaction.objectStore(
                ATTENDANCE_QUEUE_STORE
            );

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
                ATTENDANCE_QUEUE_STORE,
                'readwrite'
            );

            const store = transaction.objectStore(
                ATTENDANCE_QUEUE_STORE
            );

            const request = store.get(syncId);

            request.onsuccess = function () {
                const item = request.result;

                if (!item) {
                    reject(
                        new Error(
                            'Data absensi offline tidak ditemukan.'
                        )
                    );

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

    async function updateQueueResult(
        syncId,
        status,
        options = {}
    ) {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                ATTENDANCE_QUEUE_STORE,
                'readwrite'
            );

            const store = transaction.objectStore(
                ATTENDANCE_QUEUE_STORE
            );

            const request = store.get(syncId);

            request.onsuccess = function () {
                const item = request.result;

                if (!item) {
                    reject(
                        new Error(
                            'Data absensi offline tidak ditemukan.'
                        )
                    );

                    return;
                }

                item.status = status;

                if (
                    Object.prototype.hasOwnProperty.call(
                        options,
                        'attendance_id'
                    )
                ) {
                    item.attendance_id =
                        options.attendance_id;
                }

                if (
                    Object.prototype.hasOwnProperty.call(
                        options,
                        'error_message'
                    )
                ) {
                    item.error_message =
                        options.error_message;
                }

                if (status === 'synced') {
                    item.synced_at =
                        new Date().toISOString();
                }

                if (status === 'failed') {
                    item.failed_at =
                        new Date().toISOString();
                }

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

    async function getAllQueuedAttendance() {
        const db = await openDatabase();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction(
                ATTENDANCE_QUEUE_STORE,
                'readonly'
            );

            const store = transaction.objectStore(
                ATTENDANCE_QUEUE_STORE
            );

            const request = store.getAll();

            request.onsuccess = function () {
                resolve(request.result);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    async function syncPendingAttendance() {
        const pendingItems = await getPendingAttendance();

        if (!pendingItems.length) {
            console.log(
                '[SIM-MMU Attendance Offline] Tidak ada antrean yang perlu disinkronkan.'
            );

            return {
                success: true,
                synced: 0,
                failed: 0,
                conflicts: 0,
            };
        }

        console.log(
            '[SIM-MMU Attendance Offline] Memulai sinkronisasi:',
            pendingItems.length,
            'data'
        );

        let synced = 0;
        let failed = 0;
        let conflicts = 0;

        /*
        |--------------------------------------------------------------------------
        | Urutkan berdasarkan waktu pembuatan
        |--------------------------------------------------------------------------
        */

        pendingItems.sort((a, b) => {
            return new Date(a.created_at) - new Date(b.created_at);
        });

        for (const item of pendingItems) {
            try {
                console.log(
                    '[SIM-MMU Attendance Offline] Sinkronisasi:',
                    item.sync_id
                );

                /*
|--------------------------------------------------------------------------
| UPDATE — sinkronkan perubahan absensi yang sudah ada
|--------------------------------------------------------------------------
*/

                if (item.operation === 'update') {
                    const response = await fetch(
                        `/guru/attendance/${item.attendance_id}`,
                        {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        ?.getAttribute('content') ?? '',
                            },
                            body: JSON.stringify({
                                date: item.date,
                                meeting_number:
                                    item.meeting_number,
                                notes: item.notes ?? null,
                                students: item.students,
                            }),
                        }
                    );

                    let data = null;

                    try {
                        data = await response.json();
                    } catch (error) {
                        data = null;
                    }

                    if (response.ok) {
                        await updateQueueResult(
                            item.sync_id,
                            'synced',
                            {
                                attendance_id:
                                    item.attendance_id,
                            }
                        );

                        synced++;

                        console.log(
                            '[SIM-MMU Attendance Offline] Update berhasil:',
                            item.sync_id,
                            'attendance_id:',
                            item.attendance_id
                        );

                        continue;
                    }

                    await updateQueueResult(
                        item.sync_id,
                        'failed',
                        {
                            error_message:
                                data?.message ??
                                `Server mengembalikan HTTP ${response.status}.`,
                        }
                    );

                    failed++;

                    console.error(
                        '[SIM-MMU Attendance Offline] Update gagal:',
                        item.sync_id,
                        response.status,
                        data
                    );

                    continue;
                }

                const response = await fetch(
                    '/api/offline/attendance/sync',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            sync_id: item.sync_id,
                            operation: item.operation,
                            teaching_assignment_id:
                                item.teaching_assignment_id,
                            date: item.date,
                            meeting_number: item.meeting_number,
                            notes: item.notes ?? null,
                            students: item.students,
                        }),
                    }
                );

                let data = null;

                try {
                    data = await response.json();
                } catch (error) {
                    data = null;
                }

                /*
                |--------------------------------------------------------------------------
                | Berhasil / duplicate
                |--------------------------------------------------------------------------
                */

                if (
                    response.ok &&
                    data?.success === true &&
                    data?.ack?.sync_id === item.sync_id &&
                    (
                        data.ack.status === 'acknowledged' ||
                        data.ack.status === 'duplicate'
                    )
                ) {
                    const db = await openDatabase();

                    await new Promise((resolve, reject) => {
                        const transaction = db.transaction(
                            ATTENDANCE_QUEUE_STORE,
                            'readwrite'
                        );

                        const store = transaction.objectStore(
                            ATTENDANCE_QUEUE_STORE
                        );

                        const request = store.get(item.sync_id);

                        request.onsuccess = function () {
                            const queueItem = request.result;

                            if (!queueItem) {
                                reject(
                                    new Error(
                                        'Data antrean tidak ditemukan.'
                                    )
                                );

                                return;
                            }

                            queueItem.status = 'synced';
                            queueItem.attendance_id =
                                data.ack.attendance_id ?? null;
                            queueItem.synced_at =
                                new Date().toISOString();

                            const updateRequest =
                                store.put(queueItem);

                            updateRequest.onsuccess =
                                function () {
                                    resolve();
                                };

                            updateRequest.onerror =
                                function () {
                                    reject(
                                        updateRequest.error
                                    );
                                };
                        };

                        request.onerror = function () {
                            reject(request.error);
                        };
                    });

                    synced++;

                    console.log(
                        '[SIM-MMU Attendance Offline] Berhasil:',
                        item.sync_id,
                        'attendance_id:',
                        data.ack.attendance_id
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Conflict
                |--------------------------------------------------------------------------
                */

                if (
                    response.status === 409 &&
                    data?.conflict === true
                ) {
                    await updateQueueResult(
                        item.sync_id,
                        'conflict',
                        {
                            error_message:
                                data?.message ??
                                'Absensi mengalami konflik dengan data di server.',
                        }
                    );

                    conflicts++;

                    console.warn(
                        '[SIM-MMU Attendance Offline] Conflict:',
                        item.sync_id,
                        data?.message
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Error server / validasi
                |--------------------------------------------------------------------------
                */

                await updateQueueResult(
                    item.sync_id,
                    'failed',
                    {
                        error_message:
                            data?.message ??
                            `Server mengembalikan HTTP ${response.status}.`,
                    }
                );

                failed++;

                console.error(
                    '[SIM-MMU Attendance Offline] Gagal:',
                    item.sync_id,
                    response.status,
                    data
                );
            } catch (error) {
                /*
                |--------------------------------------------------------------------------
                | Network error
                |--------------------------------------------------------------------------
                |
                | Jangan ubah status menjadi failed.
                | Data tetap pending_sync agar dapat dicoba lagi
                | ketika koneksi kembali.
                |
                */

                console.warn(
                    '[SIM-MMU Attendance Offline] Koneksi gagal:',
                    item.sync_id,
                    error
                );
            }
        }

        const result = {
            success: true,
            synced,
            failed,
            conflicts,
        };

        console.log(
            '[SIM-MMU Attendance Offline] Sinkronisasi selesai:',
            result
        );

        return result;
    }


    /*
    |--------------------------------------------------------------------------
    | Auto Sync ketika koneksi kembali online
    |--------------------------------------------------------------------------
    */

    let attendanceSyncInProgress = false;

    async function runAttendanceAutoSync() {
        if (attendanceSyncInProgress) {
            return;
        }

        if (!navigator.onLine) {
            return;
        }

        attendanceSyncInProgress = true;

        try {
            const pendingCount = await getPendingCount();

            if (pendingCount === 0) {
                return;
            }

            console.log(
                '[SIM-MMU Attendance Offline] Koneksi kembali. Memulai auto-sync:',
                pendingCount,
                'data'
            );

            await syncPendingAttendance();
        } catch (error) {
            console.error(
                '[SIM-MMU Attendance Offline] Auto-sync gagal:',
                error
            );
        } finally {
            attendanceSyncInProgress = false;
        }
    }

    window.addEventListener('online', function () {
        console.log(
            '[SIM-MMU Attendance Offline] Koneksi kembali online.'
        );

        runAttendanceAutoSync();
    });

    /*
    |--------------------------------------------------------------------------
    | Public API
    |--------------------------------------------------------------------------
    */

    window.SimMmuAttendanceOffline = {
        openDatabase,
        cacheAssignment,
        getCachedAssignment,
        getAllCachedAssignments,
        addToQueue,
        getPendingAttendance,
        getPendingCount,
        updateQueueStatus,
        updateQueueResult,
        getAllQueuedAttendance,
        syncPendingAttendance,
        runAttendanceAutoSync,
    };
})();
