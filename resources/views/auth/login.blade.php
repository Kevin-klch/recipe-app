<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Meine Rezepte</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
</head>
<body>
    <main class="container">
        <form method="POST"
              action="{{ route('login') }}"
              class="card login-card">
            @csrf

            <div class="login-header">
                <span class="login-title">Recipe App</span>
                <p>Melde dich an, um Rezepte zu erstellen und zu entdecken.</p>
            </div>

            @if($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="form-group">
                <label for="email">E-Mail</label>
                <input
                    class="input"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Passwort</label>
                <input
                    class="input"
                    id="password"
                    type="password"
                    name="password"
                    required
                >
            </div>

            <label class="remember-row">
                <input type="checkbox" name="remember">
                <span>Eingeloggt bleiben</span>
            </label>

            <button type="submit" class="btn login-btn">
                Einloggen
            </button>
        </form>
    </main>
</body>
</html>