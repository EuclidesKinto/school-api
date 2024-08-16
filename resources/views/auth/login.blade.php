<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

      <!-- Primary Meta Tags -->
    <title>UHC Labs | Plataforma de treinamento de Hacking e Cyber Security</title>
    <meta name="title" content="UHC Labs | Plataforma de treinamento de Hacking e Cyber Security">
    <meta name="description" content="UHC Labs é uma plataforma de treinamento para hackers, onde disponibilizamos um ambiente seguro para treinar suas habilidades de forma gameficada.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://app.uhclabs.com/login">
    <meta property="og:title" content="UHC Labs | Plataforma de treinamento de Hacking e Cyber Security">
    <meta property="og:description" content="UHC Labs é uma plataforma de treinamento para hackers, onde disponibilizamos um ambiente seguro para treinar suas habilidades de forma gameficada.">
    <meta property="og:image" content="">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://app.uhclabs.com/login">
    <meta property="twitter:title" content="UHC Labs | Plataforma de treinamento de Hacking e Cyber Security">
    <meta property="twitter:description" content="UHC Labs é uma plataforma de treinamento para hackers, onde disponibilizamos um ambiente seguro para treinar suas habilidades de forma gameficada.">
    <meta property="twitter:image" content="">

    <!-- CoreUI CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>

</head>

<body class="c-app flex-row align-items-center">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card-group">
                <div class="card p-4">
                    <div class="card-body">
                        <form method="post" action="{{ url('/login') }}">
                            @csrf
                            <h1>Entrar</h1>
                            <p class="text-muted">Entre ou registre-se usando uma conta do Google</p>
                            <div class="row">
                                <div class="col">
                                    <a href="{{ url('auth/google') }}" class="btn btn-block btn-outline-primary"><i class="c-icon cib-google"></i> Login com o Google</a>
                                </div>
                            </div>
                            {{-- <h1>Login</h1>
                            <p class="text-muted">Sign In to your account</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="cil-user"></i>
                                    </span>
                                </div>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}"
                                       placeholder="Email">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="cil-lock-locked"></i>
                                    </span>
                                </div>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Password" name="password">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <button class="btn btn-primary px-4" type="submit">Login</button>
                                </div>
                                <div class="col-8 text-right">
                                    <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                        Forgot password?
                                    </a>
                                </div>
                            </div> --}}
                        </form>
                    </div>
                </div>
                {{-- <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
                    <div class="card-body text-center">
                        <div>
                            <h2>Sign up</h2>
                            <p>Sign in to start your session</p>
                            <a class="btn btn-lg btn-outline-light mt-3" href="{{ route('register') }}">Register Now!</a>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>

<!-- CoreUI -->
<script src="{{ mix('js/app.js') }}" defer></script>

</body>
</html>
