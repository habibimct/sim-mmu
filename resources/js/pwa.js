if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js')
            .then(function (registration) {
                console.log(
                    '[SIM-MMU PWA] Service Worker terdaftar:',
                    registration.scope
                );
            })
            .catch(function (error) {
                console.error(
                    '[SIM-MMU PWA] Gagal mendaftarkan Service Worker:',
                    error
                );
            });
    });
}
