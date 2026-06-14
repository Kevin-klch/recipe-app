<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle Rezepte</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-index.css') }}">
</head>

<body>
    <main class="container">

        <section class="recipes-header">
            <div>
                <span class="header">Alle Rezepte</span>
                <p>Durchsuche deine gespeicherten Lieblingsgerichte.</p>
            </div>

            <div class="recipes-actions">
                <a class="btn btn-secondary" href="{{ route('home') }}">Startseite</a>
                <a class="btn" href="{{ route('recipes.create') }}">Rezept hinzufügen</a>
            </div>
        </section>

        <div class="search-wrapper">

            <form method="GET" action="{{ route('recipes.index') }}">
                <div class="search-box">

                    <input type="text" name="search" class="input search-input" placeholder="Rezepte suchen..."
                        value="{{ request('search') }}">

                    <button type="submit" class="btn">
                        Suchen
                    </button>

                </div>
            </form>

        </div>

        @if(request('search'))
            <p>
                Suchergebnis für:
                <strong>{{ request('search') }}</strong>
            </p>
        @endif

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        @if ($recipes->isEmpty())
            <div class="card empty-card">
                <p>Noch keine Rezepte vorhanden.</p>
            </div>
        @else
            <div class="recipe-list">
                @foreach ($recipes as $recipe)
                    <a href="{{ route('recipes.show', $recipe) }}" class="card recipe-row">

                        <div class="recipe-image">
                            <img src="{{ $recipe->photo_path
                    ? asset('storage/'.$recipe->photo_path)
                    : asset('storage/recipes/nA.png') }}" alt="{{ $recipe->title }}">
                        </div>

                        <div class="recipe-content">
                            <div>
                                <span class="recipe-content-title">
                                    {{ $recipe->title }}
                                </span>

                                @if($recipe->description)
                                    <p>{{ Str::limit($recipe->description, 100) }}</p>
                                @endif
                            </div>

                            <div class="recipe-meta">
                                <span class="badge">
                                    {{ $recipe->duration_minutes ?? '?' }} Min
                                </span>

                                <span class="badge">
                                    {{ $recipe->difficulty ?? 'Easy' }}
                                </span>

                                @if($recipe->servings)
                                    <span class="badge">
                                        {{ $recipe->servings }} Pers.
                                    </span>
                                @endif

                                @if($recipe->meal_type)
                                    <span class="badge">
                                        {{ $recipe->meal_type }}
                                    </span>
                                @endif
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>
        @endif

    </main>
</body>

</html>