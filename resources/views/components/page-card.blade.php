{{--
    Titelkarte, die in den Hero hineinragt. Der Standardinhalt ist
    Badge, Titel und Untertitel; alles Weitere (stat-row, page-lead,
    page-tags, page-actions) kommt über den Slot.

        <x-page-card title="Alle Rezepte" subtitle="10 Rezepte">
            <div class="stat-row"> … </div>
        </x-page-card>
--}}
@props([
    'title',
    'subtitle' => null,
    'badge' => null,
])

<section class="card page-card">
    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
    @endif

    @if($badge)
        <span class="badge page-badge">{{ $badge }}</span>
    @endif

    <h1 class="page-title">{{ $title }}</h1>

    @if($subtitle)
        <p class="page-subtitle">{{ $subtitle }}</p>
    @endif

    {{ $slot }}
</section>
