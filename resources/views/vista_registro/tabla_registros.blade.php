<div class="col-md">
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Celular</th>
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
                @php
                    $ultimoAbono = $registro->abonos->sortByDesc('created_at')->first();
                    $totalAbonado = $registro->abonos->sum('valor');
                @endphp
                <tr>
                    <td>{{ $registro->created_at->format('d/m/Y') }}</td>
                    <td>{{ $registro->nombre }}</td>
                    <td>{{ $registro->celular }}</td>
                    <td>{{ $registro->descripcion }}</td>
                    <td>{{ number_format($registro->valor_unitario) }}</td>
                    <td>{{ number_format($registro->valor_total) }}</td>
                    <td>{{ $registro->cantidad }}</td>
                    <td>{{ $registro->estado->estado }}</td>
                    <td>{{ number_format($totalAbonado) }}</td>
                    <td>{{ number_format($registro->restante) }}</td>
                    <td>
                        @if($registro->restante > 0)
                            <button
                                class="btn btn-sm btn-warning btnAbonar"
                                data-id_registro="{{ $registro->id }}"
                                data-id_abono="{{ optional($ultimoAbono)->id }}"
                                data-valor_abono="{{ optional($ultimoAbono)->valor }}">
                                Abonar
                            </button>
                        @else
                            <span class="badge bg-success">Pagado</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $registros->links('pagination::bootstrap-5') }}
        </div>

    </div>

    <h4 class="mt-4">Dinero: {{ number_format($dineroTotal) }}</h4>
    <h4 id="infoCliente" class="mt-4"></h4>
</div>
