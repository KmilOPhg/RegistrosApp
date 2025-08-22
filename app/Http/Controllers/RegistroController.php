<?php

namespace App\Http\Controllers;

use App\Exceptions\AbonoMayorAlTotalException;
use App\Exceptions\AbonoNegativoException;
use App\Exceptions\AbonoNoEncontradoException;
use App\Http\Requests\RegistrarRequest;
use App\Models\Abono;
use App\Services\RegistroServices;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistroController extends Controller
{
    /**
     * Usamos el trait personalizado para manejar los jsons
     */
    use ResponseJson;

    /**
     * Muestra los registros de la tabla
     *
     * @return object|RedirectResponse
     */
    public function mostrarRegistros(Request $request, RegistroServices $registroServices) {
        try {
            //Retornamos el metodo ya que retorna dos cosas o un JSON o una VISTA
            return $registroServices->mostrarRegistroService($request);
        } catch (\Exception $e) {
            Log::info('Error en la vista: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error en la vista']);
        }
    }

    /**
     * Logica para crear un registro
     *
     * @param RegistrarRequest $request
     * @param RegistroServices $registroServices
     * @return JsonResponse
     * @throws AbonoMayorAlTotalException
     * @throws AbonoNegativoException
     */
    public function registrar(RegistrarRequest $request, RegistroServices $registroServices): JsonResponse
    {
        /**
         * Usamos el service que creamos para la logica del registro pasándole
         * la validación al método
         */
        $registro = $registroServices->crearRegistroService($request->validated());

        //Creamos una clase ResponseJson en traits para manejar las respuestas JSON mas facil
        return $this->createdResponse('Registro creado correctamente', $registro->toArray());
    }

    /**
     * Encargado de editar los registros con petición ajax
     *
     * @param Request $request
     * @param RegistroServices $registroServices
     * @return JsonResponse
     */
    public function editarRegistros(Request $request, RegistroServices $registroServices): JsonResponse
    {
        //Validamos si la respuesta es AJAX
        if ($request->ajax()) {
            //Almacenamos en una variable resultado el retorno de editarRegistro para acceder a sus variables
            $resultado = $registroServices->editarRegistroService($request);
            $registro = $resultado['registro'];
            $abono = $resultado['abono'];

            //Crear un nuevo abono en la tabla abono
            $this->crearAbono($request);

            //Respuesta correcta devolver codigo 200
            return $this->successResponse('Registro actualizado correctamente', [
                'registro' => $registro->toArray(),
                'abono' => $request->abono,
                'abono actual' => $abono->valor,
            ]);
        } else {
            return $this->errorResponse('No se pudo encontrar el AJAX', ['Error'], 404);
        }
    }

    public function crearAbono(Request $request)
    {
        try {
            Abono::create([
                'id_registro' => $request->id_registro,
                'valor' => $request->abono,
            ]);
        } catch (\Exception $e) {
            Log::info('Error al crear abono' . $e->getMessage());
        }
    }
}
