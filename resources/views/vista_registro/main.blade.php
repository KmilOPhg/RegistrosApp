<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Registros de productos</title>
</head>

<body id="body">
    <header class="header-ola">
        <div class="container py-5 text-center">
            <h1 class="display-4 fw-bold">Registro de clientes</h1>
        </div>

        <!-- Ola negra de fondo, cubre todo el header -->
        <svg class="ola-negra" viewBox="0 0 1200 300" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,300 C300,100 900,400 1200,200 L1200,0 L0,0 Z" />
        </svg>

        <div class="header-spacing"></div>
    </header>

    @if($errors->any())
        <div class="alert alert-danger container" id="mensaje-error">
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
            }, 4000);
        </script>
    @endif

    <div class="container page-wrapper">
        <div class="row">
            <!-- FORMULARIO -->
            <div class="col-md-4 mb-4">
                <form method="POST" action="{{ route('agregar') }}" class="card p-3 shadow-sm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <input type="text" name="cliente" class="form-control" placeholder="Nombre del cliente" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <input type="text" name="producto" class="form-control" placeholder="Producto" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" name="precio" class="form-control" placeholder="Precio unitario" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Forma de pago</label>
                        <select name="formaPago" id="formaPago" class="form-select">
                            <option value="1">Contado</option>
                            <option value="2">Crédito</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" id="labelAbono">Abono</label>
                        <input type="number" name="abono" id="campoAbono" class="form-control" placeholder="Abono inicial">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Añadir</button>
                </form>
            </div>

            <div id="contenedor_tabla" class="col-md">
                @include('vista_registro.tabla_registros', ['registros' => $registros])
            </div>
        </div>
    </div>

    <footer>
        <div style="height: 150px; overflow: hidden;" >
            <svg viewBox="0 0 500 150" preserveAspectRatio="none" style="height: 100%; width: 100%;">
                <path d="M0.00,49.85 C150.00,149.60 349.20,-49.85 500.00,49.85 L500.00,149.60 L0.00,149.60 Z" style="stroke: none; fill: #198754;"></path>
            </svg>
        </div>
    </footer>
</body>
</html>
