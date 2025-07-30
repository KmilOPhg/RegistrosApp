<table id="tabla_registros">
    <thead>
    <tr>
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
            <td>{{ $registro->nombre }}</td>
            <td> {{ $registro->descripcion }}</td>
            <td> {{ $registro->valor }}</td>
            <td> {{ $registro->estado->estado }}</td>
            <td> NO APLICA </td>
            <td> NO APLICA</td>
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
            @foreach ($registro->abonos as $abono)
                <tr>
                    <td>{{ $registro->nombre }}</td>
                    <td> {{ $registro->descripcion }}</td>
                    <td> {{ $registro->valor }}</td>
                    <td> {{ $registro->estado->estado }}</td>
                    <td> {{ $abono->valor }} </td>
                    <td> {{ $registro->restante }}</td>
                    <td>
                        @if ($registro->estado->id == 2 && $registro->abonos->sum('valor') < $registro->valor)
                            <button
                                class="actualizarAbono"
                                data-id_registro="{{ $registro->id }}"
                                data-id_abono="{{ $abono->id }}"
                                data-valor_abono="{{ $abono->valor }}"
                                type="button"> Abonar
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
