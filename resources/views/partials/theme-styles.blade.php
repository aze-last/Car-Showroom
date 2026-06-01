@php
    $activePaletteKey = App\Models\Setting::get('design_palette', 'emerald');
    $palettes = config('showroom.design.palettes');
    $palette = $palettes[$activePaletteKey] ?? $palettes['emerald'];
@endphp

<style>
    :root {
        --brand-primary: {{ $palette['primary'] }};
        --brand-primary-light: {{ $palette['primary_light'] }};
        --brand-primary-dark: {{ $palette['primary_dark'] }};
    }

    /* Override dynamic elements */
    .text-brand-primary { color: var(--brand-primary); }
    .bg-brand-primary { background-color: var(--brand-primary); }
    .border-brand-primary { border-color: var(--brand-primary); }
    
    .text-brand-primary-dark { color: var(--brand-primary-dark); }
    .bg-brand-primary-dark { background-color: var(--brand-primary-dark); }
    
    /* Smooth transition for theme switching */
    body {
        transition: background-color 0.3s ease, color 0.3s ease;
    }
</style>
