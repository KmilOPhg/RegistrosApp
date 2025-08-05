<table id="tabla_registros">
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Precio</th>
        <th>Forma De Pago</th>
        <th>Abono</th>
        <th>Restante</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($registros as $registro)
        @if($registro->abonos->isEmpty())
        {{-- Mostrar igualmente la fila aunque no tenga abonos --}}
        <tr>
            <td>{{ $registro->created_at }}</td>
            <td>{{ $registro->nombre }}</td>
            <td> {{ $registro->descripcion }}</td>
            <td> {{ $registro->valor }}</td>
            <td> {{ $registro->estado->estado }}</td>
            <td> {{ $registro->$abonos }} </td>
            <td> {{ $registro->restante }}</td>
            <td>
                @if ($registro->estado->id == 2 && $registro->abonos->sum('valor') < $registro->valor)
                    <button
                        class="actualizarAbono"
                        data-id_registro="{{ $registro->id }}"
                        data-id_abono=""
                        data-valor_abono=""
                        type="button"> Abonar
                    </button>
                @endif
            </td>
        </tr>
        @else
            @php
            //Agarramos el último abono de la tabla abonos por la relacion en Registro.php de hasMany
            $ultimoAbono = $registro->abonos->sortByDesc('created_at')->first();

            //Agarramos la suma total de los abonos de ese registro
            $totalAbonado = $registro->abonos->sum('valor');
            @endphp
            @if ($ultimoAbono)
                <tr>
                    <td>{{ $registro->created_at }}</td>
                    <td>{{ $registro->nombre }}</td>
                    <td>{{ $registro->descripcion }}</td>
                    <td>{{ $registro->valor }}</td>
                    <td>{{ $registro->estado->estado }}</td>
                    <td>{{ $totalAbonado }}</td> {{-- Ponemos el total abonado en la tabla para que se vea todo lo abonado --}}
                    <td>{{ $registro->restante }}</td>
                    <td>
                        <button
                            class="btnAbonar"
                            data-id_registro="{{ $registro->id }}"
                            data-id_abono="{{ $ultimoAbono->id }}"
                            data-valor_abono="{{ $ultimoAbono->valor }}"
                            type="button"> Abonar
                        </button>
                    </td>
                </tr>
            @endif
        @endif
    @endforeach
    </tbody>
</table>
