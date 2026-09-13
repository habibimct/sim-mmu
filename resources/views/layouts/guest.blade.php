@php
    $induk = \App\Models\Organization::where('type', 'induk')->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <style>
        html:not(.page-ready) body {
            visibility: hidden;
        }

        html.page-ready body {
            visibility: visible;
        }
    </style>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    @if ($induk?->logo_path)
        <link
            rel="icon"
            type="image/webp"
            href="{{ asset('storage/' . $induk->logo_path) }}"
        >
    @endif

    <title>
        {{ config('app.name', 'PMUB') }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="antialiased">

    {{ $slot }}

    <script>
        window.addEventListener('load', function () {
            document.documentElement.classList.add('page-ready');
        });
    </script>

</body>

</html>
