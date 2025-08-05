<table id="tabla_registros">
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Precio Unidad</th>
        <th>Precio Total</th>
        <th>Cantidad</th>
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
            <td>{{ $registro->created_at->format('d/m/Y') }}</td>
            <td>{{ $registro->nombre }}</td>
            <td> {{ $registro->descripcion }}</td>
            <td> {{ number_format($registro->valor_unitario) }}</td>
            <td> {{ number_format($registro->valor_total) }}</td>
            <td> {{ $registro->cantidad }}</td>
            <td> {{ $registro->estado->estado }}</td>
            <td> {{ number_format($registro->$abonos) }} </td>
            <td> {{ number_format($registro->restante) }}</td>
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
        </tr> {{-- Fin del muestreo de las filas de credito --}}
        @else
            @php
            //Agarramos el último abono de la tabla abonos por la relacion en Registro.php de hasMany
            $ultimoAbono = $registro->abonos->sortByDesc('created_at')->first();

            //Agarramos la suma total de los abonos de ese registro
            $totalAbonado = $registro->abonos->sum('valor');
            @endphp
            @if ($ultimoAbono)
                {{-- Muestreo de filas que son contado o ya no deben --}}
                <tr>
                    <td>{{ $registro->created_at->format('d/m/Y') }}</td>
                    <td>{{ $registro->nombre }}</td>
                    <td>{{ $registro->descripcion }}</td>
                    <td> {{ number_format($registro->valor_unitario) }}</td>
                    <td> {{ number_format($registro->valor_total) }}</td>
                    <td> {{ $registro->cantidad }}</td>
                    <td>{{ $registro->estado->estado }}</td>
                    <td>{{ number_format($totalAbonado) }}</td> {{-- Ponemos el total abonado en la tabla para que se vea todo lo abonado --}}
                    <td>{{ number_format($registro->restante) }}</td>
                    <td>
                        @if($registro->restante > 0)
                            <button
                                class="btnAbonar"
                                data-id_registro="{{ $registro->id }}"
                                data-id_abono="{{ $ultimoAbono->id }}"
                                data-valor_abono="{{ $ultimoAbono->valor }}"
                                type="button"> Abonar
                            </button>
                        @else
                            Pagado
                        @endif

                    </td>
                </tr> {{-- Fin del muestreo de finas que son contado o ya no deben --}}
            @endif
        @endif
    @endforeach
    </tbody>
</table>

<h2 id="dinero_total">Dinero: {{ number_format($dineroTotal) }}</h2>
