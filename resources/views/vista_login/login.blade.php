<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Vincula el archivo CSS local -->
    @vite(['resources/js/app.js', 'resources/css/login.css'])

    <!-- Fuente 'Audiowide' para el título -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
</head>
<body>

<!-- Contenedor del fondo animado -->
<div class="background-wrap">
    <div class="background"></div>
</div>

<form id="accesspanel" method="POST" action="{{ route('login') }}">
    <h1 id="litheader">Iniciar sesión</h1>
    <div class="inset">
        @csrf

        @if($errors->any())
            <div class="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @if($errors->any())
            <script>
                $(function () {
                    const btn = $("#go");
                    const header = $("#litheader");

                    header.removeClass("poweron");
                    btn.addClass("denied").val("Access Denied");
                });
            </script>
        @endif

        <p>
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </p>

        <p>
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </p>

        <p>
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Recordarme</label>
        </p>
    </div>
    <p class="p-container">
        <input type="submit" value="Ingresar" id="go">
    </p>
</form>

</body>
</html>
