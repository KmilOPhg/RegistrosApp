<?php

namespace App\Repositories;

use App\Models\Abono;
use App\Models\Registro;

class RegistroRepository
{
    /**
     * @param array $registro
     * @param $precioTotal
     * @return Registro
     */
    public function crearRegistroRepo(array $registro, $precioTotal) : Registro
    {
        return Registro::create([
            'nombre' => $registro['cliente'],
            'celular' => $registro['celular'],
            'descripcion' => $registro['producto'],
            'valor_unitario' => $registro['precio'],
            'valor_total' => $precioTotal,
            'cantidad' => $registro['cantidad'],
            'id_estado' => $registro['formaPago'],
        ]);
    }

    /**
     * @param $registro
     * @param $abono
     * @return Abono
     */
    public function crearAbonoRepo($registro, $abono) : Abono
    {
        //Usamos la relacion para crear el abono, asignando automaticamente el id_Registro
        return $registro->abonos()->create([
            'valor' => $abono ?? 0,
        ]);
    }
}
