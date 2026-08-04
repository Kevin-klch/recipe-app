<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile verwalten | Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-form.css') }}">
</head>

<body>

    <x-page-hero :back-url="route('home')" back-label="Zurück zur Startseite" symbol="👥" />

    <main class="container page-main">

        <x-page-card title="Profile verwalten"
            :subtitle="$users->count().' '.($users->count() === 1 ? 'Profil' : 'Profile').' angelegt'">

            <div class="stat-row">
                <x-stat icon="users" :value="$users->count()"
                    :label="$users->count() === 1 ? 'Profil' : 'Profile'" />

                <x-stat icon="servings" :value="$users->where('is_admin', true)->count()" label="mit Adminrechten" />
            </div>
        </x-page-card>

        <div class="content-grid">

            <section class="card panel">
                <h2>Neues Profil anlegen</h2>

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input class="input" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}"
                                required>
                        </div>

                        <div class="form-group full">
                            <label for="password">Passwort</label>
                            <input class="input" id="password" type="text" name="password" required>
                            <p class="field-hint">
                                Mindestens 8 Zeichen. Es wird im Klartext angezeigt, damit du es
                                weitergeben kannst.
                            </p>
                        </div>
                    </div>

                    <div class="footer-actions">
                        <button class="btn" type="submit">Profil anlegen</button>
                    </div>
                </form>
            </section>

            <section class="card panel">
                <h2>Alle Profile</h2>

                <ul class="user-list">
                    @foreach($users as $user)
                        <li class="user-row">
                            <div class="user-info">
                                <span class="user-name">
                                    {{ $user->name }}

                                    @if($user->is_admin)
                                        <span class="badge badge-user">Admin</span>
                                    @endif

                                    @if($user->id === auth()->id())
                                        <span class="badge">Du</span>
                                    @endif
                                </span>

                                <span class="user-mail">{{ $user->email }}</span>
                            </div>

                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                    onsubmit="return confirm('Möchtest du dieses Profil wirklich löschen? Alle Rezepte dieses Nutzers werden ebenfalls gelöscht.')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger" type="submit">Löschen</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </main>
</body>

</html>
