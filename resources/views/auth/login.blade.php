<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PITAYA Control</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page" style="--login-background: url('{{ asset('images/fundo_login.jpg') }}')">
    <main class="auth-card">
        <aside class="auth-brand-panel" aria-label="PITAYA Control">
            <img src="{{ asset('images/logo_pitaya.png') }}" alt="PITAYA Control">
        </aside>

        <section class="auth-form-panel" aria-labelledby="login-title">
            <div class="auth-form-content">
                <h1 id="login-title">Login</h1>

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-field">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            required
                            autofocus
                            aria-describedby="email-error"
                        >
                        @error('email')
                            <p class="auth-error" id="email-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="password">Senha</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            autocomplete="current-password"
                            required
                            aria-describedby="password-error"
                        >
                        @error('password')
                            <p class="auth-error" id="password-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="button">Entrar</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
