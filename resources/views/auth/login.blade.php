<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-form.css') }}">
</head>

<body>

    <x-page-hero symbol="🍽️" />

    <main class="container page-main">

        <section class="card page-card page-card-narrow">
            @if($errors->any())
                <div class="error-message">{{ $errors->first() }}</div>
            @endif

            @if(session('status'))
                <div class="success-message">{{ session('status') }}</div>
            @endif

            <h1 class="page-title">Meine Rezepte</h1>
            <p class="page-subtitle">Melde dich an, um Rezepte zu erstellen und zu entdecken.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">E-Mail</label>
                    <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required
                        autofocus autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input class="input" id="password" type="password" name="password" required
                        autocomplete="current-password">
                </div>

                <label class="remember-row">
                    <input type="checkbox" name="remember">
                    <span>Eingeloggt bleiben</span>
                </label>

                <button type="submit" class="btn login-btn">Einloggen</button>
            </form>
        </section>
    </main>
</body>

</html>
