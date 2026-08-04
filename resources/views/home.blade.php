<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>

    <x-page-hero symbol="🍳">
        <x-slot:lead>
            <button class="hero-btn" type="button" onclick="openInfoModal()" aria-label="Informationen anzeigen">
                @include('partials.icons.info')
            </button>
        </x-slot:lead>

        <x-slot:actions>
            <span class="hero-pill">
                👤 <span class="hero-pill-label">{{ auth()->user()->name }}</span>
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hero-pill" aria-label="Abmelden">
                    @include('partials.icons.logout')
                    <span class="hero-pill-label">Logout</span>
                </button>
            </form>
        </x-slot:actions>
    </x-page-hero>

    <main class="container page-main">

        <x-page-card :title="'Hallo, '.Str::before(auth()->user()->name, ' ')"
            subtitle="Was möchtest du heute kochen?">

            <div class="stat-row">
                <x-stat icon="book" :value="$stats['recipes']"
                    :label="$stats['recipes'] == 1 ? 'Rezept' : 'Rezepte'" />

                <x-stat icon="servings" :value="$stats['own']" label="von dir" />

                <x-stat icon="leaf" :value="$stats['ingredients']" label="Zutaten bekannt" />
            </div>

            <form class="page-search" method="GET" action="{{ route('recipes.index') }}">
                <input class="input" type="text" name="search" placeholder="Rezept suchen …"
                    aria-label="Rezept suchen">

                <button class="btn" type="submit">Suchen</button>
            </form>

            <div class="page-actions">
                <a class="btn" href="{{ route('recipes.create') }}">Rezept hinzufügen</a>
                <a class="btn btn-secondary" href="{{ route('recipes.index') }}">Alle Rezepte</a>
            </div>
        </x-page-card>

        <div class="content-grid">
            <section class="card panel">
                <h2>Zuletzt hinzugefügt</h2>

                @if ($recipes->isEmpty())
                    <p class="panel-text">Noch keine Rezepte vorhanden.</p>
                @else
                    <div class="recipe-grid">
                        @foreach ($recipes as $recipe)
                            <a href="{{ route('recipes.show', $recipe) }}" class="recipe-card">

                                <div class="recipe-image">
                                    <img src="{{ $recipe->photo_path
                                        ? asset('storage/'.$recipe->photo_path)
                                        : asset('storage/recipes/nA.png') }}" alt="{{ $recipe->title }}">
                                </div>

                                <div class="recipe-content">
                                    <div>
                                        <span class="recipe-content-title">{{ $recipe->title }}</span>

                                        @if($recipe->description)
                                            <p>{{ Str::limit($recipe->description, 80) }}</p>
                                        @endif
                                    </div>

                                    <div class="recipe-meta">
                                        <span class="badge">{{ $recipe->duration_minutes ?? '?' }} Min</span>

                                        @if($recipe->difficulty)
                                            <span class="badge">{{ $recipe->difficulty_label }}</span>
                                        @endif

                                        @if($recipe->servings)
                                            <span class="badge">
                                                {{ $recipe->servings }}
                                                {{ $recipe->servings == 1 ? 'Portion' : 'Portionen' }}
                                            </span>
                                        @endif

                                        <span class="badge badge-user">👤 {{ $recipe->user->name ?? 'Unbekannt' }}</span>
                                    </div>
                                </div>

                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </main>

    <div id="info-modal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Informationen</h2>

                <button class="modal-close" onclick="closeInfoModal()" aria-label="Schließen">×</button>
            </div>

            <div class="modal-body">
                <p>Willkommen bei unserer Rezeptsammlung.</p>

                <ul>
                    <li>Eigene Rezepte können erstellt werden.</li>
                    <li>Bitte nur passende Bilder hochladen.</li>
                    <li>Jeder Nutzer ist für seine Rezepte selbst verantwortlich.</li>
                    <li>Rezepte können jederzeit bearbeitet werden.</li>
                    <li>Bei Fragen einfach Kevin kontaktieren.</li>
                </ul>
            </div>

        </div>
    </div>

    <script>
        function openInfoModal() {
            document.getElementById('info-modal').classList.add('show');
        }

        function closeInfoModal() {
            document.getElementById('info-modal').classList.remove('show');
        }

        window.addEventListener('click', function (event) {
            if (event.target === document.getElementById('info-modal')) {
                closeInfoModal();
            }
        });
    </script>
</body>

</html>
