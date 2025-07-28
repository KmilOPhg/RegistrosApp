<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    <h1>Iniciar sesion</h1>

    @if($errors->any())
        <div class="alert alert-danger" id="mensaje-error">
            <ul class="mb-0" >
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <script>
            let mensajeError = document.getElementById('mensaje-error');
            setTimeout(() => {
                mensajeError.style.display = 'none';
            }, 2000);
        </script>
    @endif


    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="email" name="email" id="txtEmail" placeholder="Email" required>
        <input type="password" name="password" id="txtContra" placeholder="*********" required>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
