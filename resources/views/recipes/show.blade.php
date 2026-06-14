<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $recipe->title }}</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-show.css') }}">
</head>

<body>
    <main class="container">
        <a class="back-link" href="{{ route('recipes.index') }}">← Alle Rezepte</a>

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <article class="recipe-detail">
            <header class="detail-header">
                <div class="recipe-header">
                    <span class="badge">{{ $recipe->meal_type ?? 'Keine Kategorie' }}</span>
                    <span class="header">{{ $recipe->title }}</span>

                    @if($recipe->description)
                        <p>{{ $recipe->description }}</p>
                    @endif
                </div>
            </header>

            <section class="info-grid">
                <div class="card">
                    <span>Dauer</span>
                    <strong>{{ $recipe->duration_minutes ? $recipe->duration_minutes.' Min.' : '-' }}</strong>
                </div>

                <div class="card">
                    <span>Aufwand</span>
                    <strong>{{ $recipe->difficulty ?? '-' }}</strong>
                </div>

                <div class="card">
                    <span>Preis</span>
                    <strong>{{ $recipe->price_level ?? '-' }}</strong>
                </div>

                <div class="card">
                    <span>Portionen</span>
                    <strong>{{ $recipe->servings ?? '-' }}</strong>
                </div>

                <div class="card">
                    <span>Kcal</span>
                    <strong>{{ $recipe->kcal ?? '-' }}</strong>
                </div>

                <div class="card">
                    <span>Ernährung</span>
                    <strong>{{ $recipe->diet_type ?? '-' }}</strong>
                </div>
            </section>

            @if($recipe->photo_path)
                <section class="recipe-photo">
                    <img src="{{ asset('storage/'.$recipe->photo_path) }}" alt="{{ $recipe->title }}">
                </section>
            @endif

            <section class="card content-section">
                <h2>Zutaten</h2>

                @if($recipe->ingredients->isEmpty())
                    <p>Keine Zutaten vorhanden.</p>
                @else

                    <div class="ingredients-table">

                        @foreach($recipe->ingredients as $ingredient)

                            <div class="ingredient-row">

                                <div class="ingredient-amount">
                                    {{ $ingredient->amount }}
                                    {{ $ingredient->unit }}
                                </div>

                                <div class="ingredient-name">
                                    {{ $ingredient->name }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif
            </section>

            @if($recipe->instructions)
                <section class="card content-section">
                    <h2>Zubereitung</h2>
                    <p>{{ $recipe->instructions }}</p>
                </section>
            @endif

            @if($recipe->notes)
                <section class="card content-section">
                    <h2>Notizen</h2>
                    <p>{{ $recipe->notes }}</p>
                </section>
            @endif

            @if($recipe->source_url)
                <section class="card content-section">
                    <h2>Quelle</h2>
                    <a href="{{ $recipe->source_url }}" target="_blank">
                        {{ $recipe->source_url }}
                    </a>
                </section>
            @endif
        </article>

        <div class="recipe-actions">
            <a href="{{ route('recipes.edit', $recipe) }}" class="btn">
                Rezept bearbeiten
            </a>
    
            <form action="{{ route('recipes.destroy', $recipe) }}" method="POST"
                onsubmit="return confirm('Möchtest du dieses Rezept wirklich löschen?')">
                @csrf
                @method('DELETE')
    
                <button type="submit" class="btn btn-danger">
                    Rezept löschen
                </button>
            </form>
        </div>
    </main>
</body>

</html>