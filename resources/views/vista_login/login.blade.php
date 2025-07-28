<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
</head>
<body>
    <h1>Iniciar sesion</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <input type="email" name="email" id="txtEmail" placeholder="Email" required>
        <input type="password" name="password" id="txtContra" placeholder="*********" required>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
