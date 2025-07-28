<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Registros de productos</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    <h1>Registros</h1>

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

    @if(session('success'))
        <div class="alert alert-success" id="mensaje-success">
            {{ session('success') }}
        </div>

        <script>
            let mensajeSuccess = document.getElementById('mensaje-success');
            setTimeout(() => {
                mensajeSuccess.style.display = 'none';
            }, 2000);
        </script>
    @endif


    <form method="POST" action="{{ route('agregar') }}">
        @csrf

        <label>Cliente</label>
        <input type="text" name="cliente" placeholder="cliente" required>

        <label>Producto</label>
        <input type="text" name="producto" placeholder="producto" required>

        <label>Precio</label>
        <input type="number" name="precio" placeholder="precio" required>

        <label>Forma de pago</label>
        <select name="formaPago" id="formaPago">
            <option value="1">Contado</option>
            <option value="2">Credito</option>
        </select>

        <label id="labelAbono">Abono</label>
        <input type="number" name="abono" id="campoAbono" placeholder="abono">

        <button type="submit">Añadir</button>
    </form>

    @include('vista_registro.tabla_registros', ['registros' => $registros])
</body>
</html>
