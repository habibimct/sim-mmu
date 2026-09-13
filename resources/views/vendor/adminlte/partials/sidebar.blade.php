@php
    $items = app('adminlte')->menu('sidebar');
    $sidebarTheme = config('adminlte.sidebar_theme', 'light');
    $sidebarClasses = config('adminlte.classes_sidebar', 'bg-body-secondary shadow');

    $user = auth()->user();

    $organization = $user?->organizations->first();
@endphp
<aside class="app-sidebar {{ $sidebarClasses }}" @if ($sidebarTheme === 'dark') data-bs-theme="dark" @endif>
    {{-- Brand --}}
    <div class="sidebar-brand {{ config('adminlte.classes_brand') }}">
        <a href="{{ url('/') }}" class="brand-link">

            @if ($organization?->logo_path)
                <img src="{{ asset('storage/' . $organization->logo_path) }}" alt="{{ $organization->name }}"
                    class="brand-image opacity-75 shadow">
            @else
                <img src="{{ asset('images/Load.jpg') }}" alt="Logo" class="brand-image opacity-75 shadow">
            @endif

            <span class="brand-text {{ config('adminlte.classes_brand_text', 'fw-light') }}">
                <b>{{ $organization?->code ?? 'SIM-MMU' }}</b>
            </span>

        </a>
    </div>

    {{-- Menu --}}
    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="{{ __('Main navigation') }}">
            <ul class="nav sidebar-menu flex-column {{ config('adminlte.classes_sidebar_nav') }}"
                data-lte-toggle="treeview" data-accordion="false" role="menu" id="navigation">
                @foreach ($items as $item)
                    @include('adminlte::partials.menu-item', ['item' => $item])
                @endforeach
            </ul>

        </nav>
    </div>
</aside>

@once
    {{-- Inline (not @push('css'): this partial renders in the body, after the head's @stack('css')). --}}
    <style>
        .sidebar-docs-cta {
            padding: 1rem;
        }

        /* When the sidebar is collapsed to icons (and not hovered open), shrink the
                   docs button to icon-only so it doesn't overflow the narrow rail. */
        .sidebar-mini.sidebar-collapse .app-sidebar:not(:hover) .sidebar-docs-cta {
            padding: .5rem;
        }

        .sidebar-mini.sidebar-collapse .app-sidebar:not(:hover) .sidebar-docs-cta__text {
            display: none;
        }

        /* Fully-collapsed (non-mini) sidebars hide off-canvas, so hide the CTA outright. */
        .sidebar-collapse:not(.sidebar-mini) .sidebar-docs-cta {
            display: none;
        }
    </style>
@endonce
