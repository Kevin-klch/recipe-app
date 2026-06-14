<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Profile verwalten</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
</head>

<body>
    <main class="container">

        <a class="back-link" href="{{ route('home') }}">← Zurück zur Startseite</a>

        <section class="card" style="padding: 28px; margin-bottom: 24px;">
            <h1>Profile verwalten</h1>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input class="input" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>E-Mail</label>
                        <input class="input" type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Passwort</label>
                        <input class="input" type="text" name="password" required>
                    </div>
                </div>

                <button class="btn" type="submit" style="margin-top: 20px;">
                    Profil anlegen
                </button>
            </form>
        </section>

        <section class="card" style="padding: 28px;">
            <h2>Alle Profile</h2>

            @foreach($users as $user)
                <div
                    style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <p style="margin: 0;">
                        <strong>{{ $user->name }}</strong>
                        — {{ $user->email }}

                        @if($user->is_admin)
                            <span class="badge">Admin</span>
                        @endif
                    </p>

                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                            onsubmit="return confirm('Möchtest du dieses Profil wirklich löschen? Alle Rezepte dieses Nutzers werden ebenfalls gelöscht.')">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger" type="submit">
                                Löschen
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </section>

    </main>
</body>

</html>