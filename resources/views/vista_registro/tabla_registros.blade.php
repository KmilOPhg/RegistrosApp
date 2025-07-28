<table>
    <thead>
    <tr>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Precio</th>
        <th>Forma De Pago</th>
        <th>Abono</th>
    </tr>
    </thead>
    <tbody>
    @foreach($registros as $registro)
    <tr>
        <td>{{ $registro->nombre }}</td>
        <td> {{ $registro->descripcion }}</td>
        <td> {{ $registro->valor }}</td>
        <td> {{ $registro->estado->estado }}</td>
        <td> {{ $registro->abono }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
