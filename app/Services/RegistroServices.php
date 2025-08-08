<?php

namespace App\Services;

use App\Exceptions\AbonoInvalidoException;
use App\Http\Requests\RegistrarRequest;
use App\Models\Registro;
use Illuminate\Support\Facades\DB;

class RegistroServices
{
    /**
     * @param array $validarRegistro
     * @return Registro
     * @throws AbonoInvalidoException
     *
     * Servicio encargado de crear el registro donde le pasamos el $request->validated()
     * Y se toman los campos desde ahi
     */
    public function crearRegistro(array $validarRegistro): Registro {
        return DB::transaction(function () use ($validarRegistro) {

            //Si el abono es nulo lo ponemos como cero
            $abono = $validarRegistro['abono'] ?? 0;

            /**
             * Guardamos cantidad y precio para operar
             */
            $cantidad = $validarRegistro['cantidad'];
            $precio = $validarRegistro['precio'];

            //Calculamos el precio total con cantidad y valor
            $precioTotal = $cantidad * $precio;

            //Como es un servicio lanzamos una excepcion para que lo tome el catch del controlador
            if ($abono > $precioTotal) throw new AbonoInvalidoException($precioTotal);

            //Creamos el registro aqui ponemos los campos que van en la base de datos
            $registro = Registro::create([
                'nombre' => $validarRegistro['cliente'],
                'celular' => $validarRegistro['celular'],
                'descripcion' => $validarRegistro['producto'],
                'valor_unitario' => $validarRegistro['precio'],
                'valor_total' => $precioTotal,
                'cantidad' => $validarRegistro['cantidad'],
                'id_estado' => $validarRegistro['formaPago'],
            ]);

            //Creamos el abono en la tabla abonos con el id del registro creado
            if ($abono >= 0 || $registro['formaPago'] == 2) {
                //Usamos la relacion para crear el abono, asignando automaticamente el id_Registro
                $registro->abonos()->create([
                    'valor' => $abono ?? 0,
                ]);
            }

            return $registro;
        });
    }
}
