const DB_NAME = 'sim-mmu-offline';
const DB_VERSION = 7;
const STORE_NAME = 'offline_auth';

function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = event => {
            const db = event.target.result;

            /*
            |--------------------------------------------------------------------------
            | FINANCE
            |--------------------------------------------------------------------------
            */

            if (!db.objectStoreNames.contains('transaction_queue')) {
                const store = db.createObjectStore('transaction_queue', {
                    keyPath: 'sync_id',
                });

                store.createIndex('status', 'status', {
                    unique: false,
                });

                store.createIndex('created_at', 'created_at', {
                    unique: false,
                });
            }

            /*
            |--------------------------------------------------------------------------
            | ATTENDANCE CACHE
            |--------------------------------------------------------------------------
            */

            if (!db.objectStoreNames.contains('attendance_cache')) {
                const store = db.createObjectStore('attendance_cache', {
                    keyPath: 'assignment_id',
                });

                store.createIndex('updated_at', 'updated_at', {
                    unique: false,
                });
            }

            /*
            |--------------------------------------------------------------------------
            | ATTENDANCE QUEUE
            |--------------------------------------------------------------------------
            */

            if (!db.objectStoreNames.contains('attendance_queue')) {
                const store = db.createObjectStore('attendance_queue', {
                    keyPath: 'sync_id',
                });

                store.createIndex('status', 'status', {
                    unique: false,
                });

                store.createIndex('created_at', 'created_at', {
                    unique: false,
                });

                store.createIndex('operation', 'operation', {
                    unique: false,
                });

                store.createIndex('attendance_id', 'attendance_id', {
                    unique: false,
                });
            }

            /*
            |--------------------------------------------------------------------------
            | OFFLINE AUTH
            |--------------------------------------------------------------------------
            */

            if (!db.objectStoreNames.contains('offline_auth')) {
                db.createObjectStore('offline_auth', {
                    keyPath: 'user_id',
                });
            }

            /*
|--------------------------------------------------------------------------
| OFFLINE CREDENTIAL
|--------------------------------------------------------------------------
*/

            if (!db.objectStoreNames.contains('offline_credential')) {
                db.createObjectStore('offline_credential', {
                    keyPath: 'user_id',
                });
            }
        };

        request.onsuccess = event => {
            resolve(event.target.result);
        };

        request.onerror = () => {
            reject(request.error);
        };
    });
}

async function saveOfflineIdentity(identity) {
    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            STORE_NAME,
            'readwrite'
        );

        const store = transaction.objectStore(STORE_NAME);

        store.put({
            user_id: identity.user_id,
            teacher_id: identity.teacher_id,
            name: identity.name,
            email: identity.email,
            role: identity.role,
            saved_at: new Date().toISOString(),
        });

        transaction.oncomplete = () => {
            resolve(true);
        };

        transaction.onerror = () => {
            reject(transaction.error);
        };
    });
}

async function saveOfflineCredential(userId, credential) {
    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            'offline_credential',
            'readwrite'
        );

        const store = transaction.objectStore('offline_credential');

        store.put({
            user_id: userId,
            credential: credential,
            saved_at: new Date().toISOString(),
        });

        transaction.oncomplete = () => {
            resolve(true);
        };

        transaction.onerror = () => {
            reject(transaction.error);
        };
    });
}

async function getOfflineCredential(userId) {
    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            'offline_credential',
            'readonly'
        );

        const store = transaction.objectStore('offline_credential');

        const request = store.get(userId);

        request.onsuccess = () => {
            resolve(request.result ?? null);
        };

        request.onerror = () => {
            reject(request.error);
        };
    });
}

async function clearOfflineCredential(userId) {
    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            'offline_credential',
            'readwrite'
        );

        const store = transaction.objectStore('offline_credential');

        store.delete(userId);

        transaction.oncomplete = () => {
            resolve(true);
        };

        transaction.onerror = () => {
            reject(transaction.error);
        };
    });
}

async function createOfflineCredential(userId) {
    const keyPair = await crypto.subtle.generateKey(
        {
            name: 'ECDSA',
            namedCurve: 'P-256',
        },
        false,
        ['sign', 'verify']
    );

    const publicKey = await crypto.subtle.exportKey(
        'jwk',
        keyPair.publicKey
    );

    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            'offline_credential',
            'readwrite'
        );

        const store = transaction.objectStore('offline_credential');

        store.put({
            user_id: userId,
            public_key: publicKey,
            private_key: keyPair.privateKey,
            saved_at: new Date().toISOString(),
        });

        transaction.oncomplete = () => {
            resolve(true);
        };

        transaction.onerror = () => {
            reject(transaction.error);
        };
    });
}

async function deriveOfflinePin(pin, salt) {
    const encoder = new TextEncoder();

    const baseKey = await crypto.subtle.importKey(
        'raw',
        encoder.encode(pin),
        'PBKDF2',
        false,
        ['deriveBits']
    );

    const bits = await crypto.subtle.deriveBits(
        {
            name: 'PBKDF2',
            salt,
            iterations: 150000,
            hash: 'SHA-256',
        },
        baseKey,
        256
    );

    return new Uint8Array(bits);
}

async function setupOfflineCredential(userId, pin) {
    if (!userId || !pin) {
        throw new Error('User ID dan PIN wajib diisi.');
    }

    if (!/^\d{6}$/.test(pin)) {
        throw new Error('PIN offline harus terdiri dari 6 digit.');
    }

    const salt = crypto.getRandomValues(
        new Uint8Array(16)
    );

    const pinVerifier = await deriveOfflinePin(
        pin,
        salt
    );

    const keyPair = await crypto.subtle.generateKey(
        {
            name: 'ECDSA',
            namedCurve: 'P-256',
        },
        false,
        ['sign', 'verify']
    );

    const publicKey = await crypto.subtle.exportKey(
        'jwk',
        keyPair.publicKey
    );

    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            'offline_credential',
            'readwrite'
        );

        const store = transaction.objectStore(
            'offline_credential'
        );

        store.put({
            user_id: userId,
            public_key: publicKey,
            private_key: keyPair.privateKey,
            pin_salt: bytesToBase64(salt),
            pin_verifier: bytesToBase64(pinVerifier),
            saved_at: new Date().toISOString(),
        });

        transaction.oncomplete = () => {
            resolve(true);
        };

        transaction.onerror = () => {
            reject(transaction.error);
        };
    });
}

async function verifyOfflinePin(userId, pin) {
    const credential = await getOfflineCredential(userId);

    if (!credential) {
        return false;
    }

    if (!credential.pin_salt || !credential.pin_verifier) {
        return false;
    }

    if (!/^\d{6}$/.test(pin)) {
        return false;
    }

    const salt = base64ToBytes(credential.pin_salt);

    const verifier = await deriveOfflinePin(
        pin,
        salt
    );

    const storedVerifier = base64ToBytes(
        credential.pin_verifier
    );

    if (verifier.length !== storedVerifier.length) {
        return false;
    }

    let difference = 0;

    for (let i = 0; i < verifier.length; i++) {
        difference |= verifier[i] ^ storedVerifier[i];
    }

    return difference === 0;
}

async function loginOffline(login, pin) {
    const identity = await findOfflineIdentity(login);

    if (!identity) {
        return {
            success: false,
            message: 'Akun Guru belum terdaftar untuk akses offline.',
        };
    }

    if (identity.role !== 'guru') {
        return {
            success: false,
            message: 'Login offline hanya tersedia untuk Guru.',
        };
    }

    const credential = await getOfflineCredential(
        identity.user_id
    );

    if (!credential) {
        return {
            success: false,
            message: 'Perangkat belum terdaftar untuk akses offline.',
        };
    }

    const validPin = await verifyOfflinePin(
        identity.user_id,
        pin
    );

    if (!validPin) {
        return {
            success: false,
            message: 'PIN offline salah.',
        };
    }

    return {
        success: true,
        identity,
    };
}

function bytesToBase64(bytes) {
    let binary = '';

    bytes.forEach(byte => {
        binary += String.fromCharCode(byte);
    });

    return btoa(binary);
}

function base64ToBytes(base64) {
    const binary = atob(base64);

    return Uint8Array.from(binary, char => char.charCodeAt(0));
}

async function verifyOfflineCredential(userId, challengeText) {
    const credential = await getOfflineCredential(userId);

    if (!credential) {
        return false;
    }

    const publicKey = await crypto.subtle.importKey(
        'jwk',
        credential.public_key,
        {
            name: 'ECDSA',
            namedCurve: 'P-256',
        },
        true,
        ['verify']
    );

    const data = new TextEncoder().encode(challengeText);

    const signature = await crypto.subtle.sign(
        {
            name: 'ECDSA',
            hash: 'SHA-256',
        },
        credential.private_key,
        data
    );

    return crypto.subtle.verify(
        {
            name: 'ECDSA',
            hash: 'SHA-256',
        },
        publicKey,
        signature,
        data
    );
}

async function getOfflineIdentity() {
    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            STORE_NAME,
            'readonly'
        );

        const store = transaction.objectStore(STORE_NAME);

        const request = store.getAll();

        request.onsuccess = () => {
            resolve(request.result[0] ?? null);
        };

        request.onerror = () => {
            reject(request.error);
        };
    });
}

async function findOfflineIdentity(login) {
    const identity = await getOfflineIdentity();

    if (!identity) {
        return null;
    }

    const normalizedLogin = String(login)
        .trim()
        .toLowerCase();

    const email = String(identity.email ?? '')
        .trim()
        .toLowerCase();

    if (normalizedLogin === email) {
        return identity;
    }

    return null;
}

async function clearOfflineIdentity() {
    const db = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction(
            STORE_NAME,
            'readwrite'
        );

        const store = transaction.objectStore(STORE_NAME);

        store.clear();

        transaction.oncomplete = () => {
            resolve(true);
        };

        transaction.onerror = () => {
            reject(transaction.error);
        };
    });
}

async function saveCurrentUserOffline() {
    if (!window.simMmuOfflineIdentity) {
        return false;
    }

    const identity = window.simMmuOfflineIdentity;

    if (!identity.user_id || identity.role !== 'guru') {
        return false;
    }

    return saveOfflineIdentity(identity);
}

window.SimMmuOfflineAuth = {
    openDatabase,
    saveOfflineIdentity,
    getOfflineIdentity,
    clearOfflineIdentity,
    saveCurrentUserOffline,
    saveOfflineCredential,
    getOfflineCredential,
    clearOfflineCredential,
    createOfflineCredential,
    verifyOfflineCredential,
    deriveOfflinePin,
    bytesToBase64,
    base64ToBytes,
    setupOfflineCredential,
    verifyOfflinePin,
    loginOffline,
    findOfflineIdentity,
};
