@if (config('adminlte.preloader', false))

    <div id="pmub-preloader">

        <div class="pmub-preloader-content">

            <img
                src="{{ asset('images/Load.jpg') }}"
                alt="PMUB"
                class="pmub-preloader-logo"
            >

            <div class="pmub-preloader-spinner"></div>

            <div class="pmub-preloader-text">
                PMUB
            </div>

        </div>

    </div>

    <style>
        #pmub-preloader {
            position: fixed !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;

            display: flex !important;
            align-items: center !important;
            justify-content: center !important;

            background: #f8fafc !important;

            z-index: 99999 !important;

            opacity: 1;
            visibility: visible;
        }

        .pmub-preloader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .pmub-preloader-logo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .15);
        }

        .pmub-preloader-spinner {
            width: 32px;
            height: 32px;
            margin-top: 24px;

            border: 3px solid #dbeafe;
            border-top-color: #2563eb;

            border-radius: 50%;

            animation: pmub-spin .8s linear infinite;
        }

        .pmub-preloader-text {
            margin-top: 12px;

            font-size: 14px;
            font-weight: 600;

            letter-spacing: .08em;

            color: #475569;
        }

        @keyframes pmub-spin {
            to {
                transform: rotate(360deg);
            }
        }

        #pmub-preloader.pmub-preloader-hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;

            transition:
                opacity .25s ease,
                visibility .25s ease;
        }
    </style>

    <script>
        window.addEventListener('load', function () {

            const preloader =
                document.getElementById('pmub-preloader');

            if (!preloader) {
                return;
            }

            preloader.classList.add(
                'pmub-preloader-hide'
            );

            setTimeout(function () {

                preloader.remove();

            }, 300);

        });
    </script>

@endif
