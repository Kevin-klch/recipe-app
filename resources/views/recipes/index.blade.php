<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle Rezepte | Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>

    <x-page-hero :back-url="route('home')" back-label="Zurück zur Startseite" symbol="📚">
        <x-slot:actions>
            <a class="hero-btn" href="{{ route('recipes.create') }}" aria-label="Rezept hinzufügen">
                @include('partials.icons.pencil')
            </a>
        </x-slot:actions>
    </x-page-hero>

    <main class="container page-main">

        <x-page-card title="Alle Rezepte"
            :subtitle="request('search')
                ? $recipes->count().' '.($recipes->count() === 1 ? 'Treffer' : 'Treffer').' für „'.request('search').'“'
                : $recipes->count().' '.($recipes->count() === 1 ? 'Rezept' : 'Rezepte').' in deiner Sammlung'">

            <form class="page-search" method="GET" action="{{ route('recipes.index') }}">
                <input class="input" type="text" name="search" placeholder="Rezepte suchen …"
                    value="{{ request('search') }}" aria-label="Rezepte suchen">

                <button class="btn" type="submit">Suchen</button>
            </form>

            <div class="page-actions">
                <a class="btn" href="{{ route('recipes.create') }}">Rezept hinzufügen</a>

                @if(request('search'))
                    <a class="btn btn-secondary" href="{{ route('recipes.index') }}">Suche zurücksetzen</a>
                @endif
            </div>
        </x-page-card>

        <div class="content-grid">
            <section class="card panel">
                <h2>{{ request('search') ? 'Suchergebnis' : 'Neueste zuerst' }}</h2>

                @if ($recipes->isEmpty())
                    <p class="panel-text">
                        @if(request('search'))
                            Zu „{{ request('search') }}“ wurde nichts gefunden.
                        @else
                            Noch keine Rezepte vorhanden.
                        @endif
                    </p>
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
                                            <p>{{ Str::limit($recipe->description, 100) }}</p>
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

                                        @if($recipe->meal_type)
                                            <span class="badge">{{ $recipe->meal_type }}</span>
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
</body>

</html>
