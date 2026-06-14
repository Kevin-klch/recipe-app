<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Rezepte</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>
    <main class="container">
        <section class="hero">
            <div class="header">
                <span>Meine Rezepte</span>
            </div>

            <div class="hero-actions">
                <a class="btn" href="{{ route('recipes.create') }}">Rezept hinzufügen</a>
                <a class="btn btn-secondary" href="{{ route('recipes.index') }}">Alle Rezepte</a>
            </div>
        </section>

        <section>
            <h2>Zuletzt hinzugefügt</h2>

            @if ($recipes->isEmpty())
            <div class="card empty-card">
                <p>Noch keine Rezepte vorhanden.</p>
            </div>
            @else
                <div class="recipe-grid">
                    @foreach ($recipes as $recipe)
                            <a href="{{ route('recipes.show', $recipe) }}" class="card recipe-card">

                                <div class="recipe-image">
                                    <img src="{{ $recipe->photo_path
                        ? asset('storage/'.$recipe->photo_path)
                        : asset('storage/recipes/nA.png') }}" alt="{{ $recipe->title }}">
                                </div>

                                <div class="recipe-content">
                                    <div>
                                        <span class="recipe-content-title">{{ $recipe->title }}</span>

                                        @if($recipe->description)
                                            <p style="margin: 5px 0">{{ Str::limit($recipe->description, 80) }}</p>
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
                                    </div>

                                </div>

                            </a>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</body>

</html>