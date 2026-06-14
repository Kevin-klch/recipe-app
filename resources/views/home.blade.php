<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Rezepte</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <script>
        function openInfoModal() {
            document.getElementById('info-modal').classList.add('show');
        }

        function closeInfoModal() {
            document.getElementById('info-modal').classList.remove('show');
        }

        window.addEventListener('click', function (event) {
            const modal = document.getElementById('info-modal');

            if (event.target === modal) {
                closeInfoModal();
            }
        });
    </script>
</head>

<div id="info-modal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Informationen</h2>

            <button class="modal-close" onclick="closeInfoModal()">
                ×
            </button>
        </div>

        <div class="modal-body">
            <p>
                Willkommen bei unserer Rezeptsammlung.
            </p>

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

<body>

    <main class="container">
        <div class="top-bar">
            <button class="info-button" onclick="openInfoModal()">
                i
            </button>

            <div class="hero-top-right">
                <span class="user-badge">
                    👤 {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <section class="hero">
            <div class="header">
                <span>Startseite</span>
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

                                        <span class="badge badge-user">
                                            👤 {{ $recipe->user->name }}
                                        </span>
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