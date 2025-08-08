<?php

namespace App\Services;

use App\Exceptions\AbonoInvalidoException;
use App\Exceptions\AbonoNoEncontradoException;
use App\Http\Requests\RegistrarRequest;
use App\Models\Abono;
use App\Models\Registro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Integer;

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
    public function crearRegistroService(array $validarRegistro): Registro {
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

    /**
     * @param Request $request
     * @return array
     *
     * IMPORTANTISIMO LOS VALORES DE RETORNO
     * Si vas a retornar un array pon de retorno:array ya que esto retorna
     * return ['registro' => $registro,'abono' => $abono];
     * Que es un array y si no especificas ese retorno todo explota
     */
    public function editarRegistroService(Request $request): array {
        return DB::transaction(function () use ($request) {

            //Obtenemos los registros y los abonos por ID con el request
            $registro = Registro::find($request->id_registro);
            $abono = Abono::find($request->id_abono);

            //Si no encuentra el abono lanza una excepcion personalizada
            if (!$abono) {
                throw new AbonoNoEncontradoException();
            }

            //Validar que el valor del abono sea válido
            if ($request->abono <= 0 || $request->abono > $registro->restante) {
                throw new AbonoInvalidoException();
            }

            //Retornamos un arreglo con registro y abono para usarlos en el controlador
            return [
                'registro' => $registro,
                'abono' => $abono,
            ];
        });
    }

    /**
     * @return void
     *
     * Caluclar el total del dinero que se tiene
     */
    public function calcularTotalSevice(): float {
        $registrosTodos = Registro::with('abonos')->get();

        //Sumar todos los abonos de los registros con crédito
        $sumaCreditos = $registrosTodos->where('id_estado', 2)
            /**
             * flatMap: Devuelve Una sola colección con todos los abonos de todos los registros
             * sin importar cuántos tenga cada uno y podemos operar
             * ESTO ES INCREIBLE
             */
            ->flatMap->abonos
            ->sum('valor');

        //Sumar los valores totales de los registros al contado
        $sumaContados = $registrosTodos->where('id_estado', 1)
            ->sum('valor_total');

        return $sumaCreditos + $sumaContados;
    }
}
