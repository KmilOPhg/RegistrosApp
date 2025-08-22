<?php

namespace App\Repositories;

use App\Models\Abono;
use App\Models\Registro;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * @param $celular
     * @return Collection
     */
    public function buscarCliente($celular) : Collection
    {
        //Buscar cliente por el celular
        return Registro::where('celular', 'like', "%$celular%")->with('abonos')->get();
    }

    /**
     * @param $celular
     * @param $paginate
     * @return LengthAwarePaginator
     */
    public function obtenerRegistrosConFiltro($celular, $paginate) : LengthAwarePaginator
    {
        //Obtenemos los registros con paginacion y relacionamos con estado y abonos
        return Registro::when($celular, function ($query) use ($celular) {
            //Retornamos la consulta SQL
            return $query->where('celular', 'like', "%$celular%");
        })->with('estado', 'abonos')->paginate($paginate);
    }

    /**
     * @return Collection
     */
    public function obtenerAbonos() : Collection
    {
        //Obtenemos todos los abonos con la relacion de registro
        return Abono::with('registro')->get();
    }

    public function obtenerRegistros() : Collection
    {
        //Obtenemos todos los registros con la relacion de estado y abonos
        return Registro::with( 'abonos')->get();
    }

    /**
     * @param $id_registro
     * @return Registro|null
     */
    public function obtenerIdRegistro($id_registro) : ?Registro
    {
        //Obtenemos los registros ID con el request
        return Registro::find($id_registro);
    }

    /**
     * @param $id_abono
     * @return Abono|null
     */
    public function obtenerIdAbonos($id_abono) : ?Abono
    {
        //Obtenemos los abonos ID con el request
        return Abono::find($id_abono);
    }
}
