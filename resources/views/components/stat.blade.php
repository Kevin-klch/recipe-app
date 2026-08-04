{{--
    Eine Kennzahl der stat-row: rundes Icon, Wert, Beschriftung.

        <x-stat icon="clock" value="45 Min" label="Dauer" />

    Verfügbare Icons siehe resources/views/partials/icons/.
--}}
@props([
    'icon' => 'clock',
    'value',
    'label',
])

<div class="stat">
    <span class="stat-icon" aria-hidden="true">
        @include('partials.icons.' . $icon)
    </span>

    <span class="stat-text">
        <span class="stat-value">{{ $value }}</span>
        <span class="stat-label">{{ $label }}</span>
    </span>
</div>
