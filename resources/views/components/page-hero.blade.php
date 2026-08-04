{{--
    Farbband bzw. Foto am Seitenkopf, mit runden Chips darauf.

    Ohne Foto wird das Band flach (page-hero-flat) und zeigt ein Symbol.

        <x-page-hero :back-url="route('home')" back-label="Zur Startseite">
            <x-slot:actions>
                … zusätzliche Chips rechts neben dem Theme-Switch …
            </x-slot:actions>
        </x-page-hero>
--}}
@props([
    'backUrl' => null,
    'backLabel' => 'Zurück',
    'photo' => null,
    'alt' => '',
    'symbol' => '🍽️',
])

<header class="page-hero{{ $photo ? '' : ' page-hero-flat' }}">
    @if($photo)
        <img src="{{ $photo }}" alt="{{ $alt }}">
    @else
        <div class="page-hero-symbol" aria-hidden="true">{{ $symbol }}</div>
    @endif

    <div class="hero-bar">
        @if($backUrl)
            <a class="hero-btn" href="{{ $backUrl }}" aria-label="{{ $backLabel }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 5l-7 7 7 7" />
                </svg>
            </a>
        @else
            {{ $lead ?? '' }}
        @endif

        <div class="hero-bar-right">
            @include('partials.theme-toggle')

            {{ $actions ?? '' }}
        </div>
    </div>
</header>
