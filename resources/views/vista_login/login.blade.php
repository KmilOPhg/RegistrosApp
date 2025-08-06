<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/login.css'])
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<form method="POST" action="{{ route('login') }}">
    <h1>Iniciar sesión</h1>
    <div class="inset">
        @csrf

        @if($errors->any())
            <div class="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
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
        <input type="submit" value="Ingresar">
    </p>
</form>

</body>
</html>
