<script>
    document.addEventListener('DOMContentLoaded', function () {

        let deferredPrompt = null;

        const installButtons =
            document.querySelectorAll('[data-pwa-install]');

        if (!installButtons.length) {
            return;
        }

        installButtons.forEach(function (button) {
            button.style.display = 'none';
        });

        window.addEventListener('beforeinstallprompt', function (event) {

            event.preventDefault();

            deferredPrompt = event;

            installButtons.forEach(function (button) {
                button.style.display = '';
            });
        });

        installButtons.forEach(function (button) {

            button.addEventListener('click', async function () {

                if (!deferredPrompt) {
                    return;
                }

                deferredPrompt.prompt();

                const result = await deferredPrompt.userChoice;

                console.log(
                    'Hasil instalasi SIM-MMU:',
                    result.outcome
                );

                deferredPrompt = null;

                installButtons.forEach(function (button) {
                    button.style.display = 'none';
                });
            });

        });

        window.addEventListener('appinstalled', function () {

            console.log('SIM-MMU berhasil diinstall.');

            deferredPrompt = null;

            installButtons.forEach(function (button) {
                button.style.display = 'none';
            });

        });

    });
</script>
