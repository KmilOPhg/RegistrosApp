<?php

namespace App\Http\Controllers;

use App\Exceptions\AbonoInvalidoException;
use App\Http\Requests\RegistrarRequest;
use App\Models\Abono;
use App\Models\Registro;
use App\Services\RegistroServices;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistroController extends Controller
{
    /**
     * Muestra los registros de la tabla
     *
     * @return Factory|View|Application|RedirectResponse|object
     */
    public function mostrarRegistros(Request $request) {
        try {
            //Agarramos el celular del cliente
            $celular = $request->get('celular');
            $deudaCliente = null;

            /**
             * Este if se encarga de que solo cuando se hayan ingresado
             * los 10 digitos de un celular haga el cálculo de cuanto deben
             * con el restante que tienen
             */
            if($celular && strlen($celular) === 10) {
                $clienteEncontrado = Registro::where('celular', $celular)->with('abonos')->get();

                if($clienteEncontrado->isNotEmpty()) {
                    $nombreCliente = $clienteEncontrado->first()->nombre;
                    $deuda = $clienteEncontrado->sum('restante');

                    /**
                     * Guardamos el nombre y la deuda total en este arreglo
                     * y lo ponemos en el return JSON de esta funcion para luego
                     * usarlo en el JS ya que lo retornamos como JSON
                     */
                    $deudaCliente = [
                        'nombre' => $nombreCliente,
                        'deuda' => $deuda,
                    ];
                }
            }

            //Aplicamos un filtro con when, tomamos el celular y lo ponemos en funcion
            $registros = Registro::when($celular, function ($query) use ($celular) {
                //Retornamos la consulta SQL
                return $query->where('celular', 'like', "%$celular%");
            })->with('estado', 'abonos')->paginate(7);

            $abonos = Abono::with('registro')->get();
            $dineroTotal = $this->calcularDineroTotal();

            //Miramos si la respuesta es AJAX
            if ($request->ajax()) {
                $view = view('vista_registro.tabla_registros', compact('registros', 'abonos', 'dineroTotal'))->render();

                //Si es AJAX retornamos el html
                return response()->json([
                    'html' => $view,
                    'deudaCliente' => $deudaCliente,
                ]);
            }

            //Si no es AJAX entonces retornamos la vista normal
            return view('vista_registro.main', compact('registros', 'abonos', 'dineroTotal'));
        } catch (\Exception $e) {
            Log::info('Error en la vista' . $e->getMessage());
            return back()->withErrors(['error' => 'Error en la vista']);
        }
    }

    /**
     * Logica para crear un registro
     *
     * @param RegistrarRequest $request
     * @param RegistroServices $registroServices
     * @return JsonResponse
     */
    public function registrar(RegistrarRequest $request, RegistroServices $registroServices): JsonResponse
    {
        try {
            $registro = $registroServices->crearRegistro($request->validated());

            return response()->json([
                'code' => 200,
                'msg' => 'success',
                'registro' => $registro,
            ]);
        } catch (AbonoInvalidoException $exception){
            Log::error('No se puede abonar mas del total' . $exception->getMessage());

            return response()->json([
                'code' => 422,
                'msg' => 'error',
                'error' => $exception->getMessage(),
            ]);
        } catch (\Exception $exception) {
            Log::error('Error en el registro' . $exception->getMessage());

            return response()->json([
                'code' => 500,
                'msg' => 'error',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Encargado de editar los registros con peticion ajax
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function editarRegistros(Request $request): JsonResponse
    {
        //Validamos si la respuesta es AJAX
        if ($request->ajax()) {
            DB::beginTransaction();

            try {
                //Obtenemos los registros y los abonos por ID con el request
                $registro = Registro::find($request->id_registro);
                $abono = Abono::find($request->id_abono);

                //Si no encuentra el abono
                if (!$abono) {
                    DB::rollBack();
                    return response()->json([
                        'code' => 404,
                        'msg' => 'error',
                        'message' => 'Abono no encontrado'
                    ]);
                }

                //Validar que el valor del abono sea válido
                if ($request->abono <= 0 || $request->abono > $registro->restante) {
                    DB::rollBack();
                    return response()->json([
                        'code' => 422,
                        'msg' => 'error',
                        'message' => 'El valor del abono debe ser mayor que 0 y menor o igual al restante',
                    ]);
                }

                //Crear un nuevo abono en la tabla abono
                $this->crearAbono($request);

                DB::commit();

                //Respuesta correcta devolver codigo 200
                return response()->json([
                    'code' => 200,
                    'msg' => 'success',
                    'message' => 'Encontrado correctamente',
                    'registro' => $registro,
                    'abono' => $request->abono,
                    'abono actual' => $abono->valor,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                //Y pues el catch
                return response()->json([
                    'code' => 500,
                    'msg' => 'error',
                    'message' => 'Ocurrió un error al procesar la solicitud.',
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            return response()->json([
                'code' => 404,
                'msg' => 'error',
                'message' => 'No se pudo encontrar el AJAX',
            ]);
        }
    }

    public function crearAbono(Request $request){
        try {
            Abono::create([
                'id_registro' => $request->id_registro,
                'valor' => $request->abono,
            ]);
        } catch (\Exception $e) {
            Log::info('Error al crear abono' . $e->getMessage());
        }
    }

    function calcularDineroTotal(): float
    {
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
