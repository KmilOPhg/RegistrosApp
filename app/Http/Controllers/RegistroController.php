<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Registro;
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
            $registros = Registro::with('estado', 'abonos')->get();
            $abonos = Abono::with('registro')->get();

            if ($request->ajax()) {
                $view = view('vista_registro.tabla_registros', compact('registros', 'abonos'));

                return response()->json([
                    'html' => $view
                ]);
            }

            return view('vista_registro.main', compact('registros', 'abonos'));
        } catch (\Exception $e) {
            Log::info('Error en la vista' . $e->getMessage());
            return back()->withErrors(['error' => 'Error en la vista']);
        }
    }

    /**
     * Logica para crear un registro
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function registrar(Request $request){
        //Validamos los datos del formulario, se tiene que poner los campos del formulario
        $validarRegistro = $request->validate([
            'cliente' => 'required|string|max:255',
            'producto' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'formaPago' => 'required|integer|in:1,2', //1: Contado, 2: Credito
            'abono' => 'nullable|numeric|min:0',
        ]);

        //Si el abono es nulo lo ponemos como cero
        $abono = $validarRegistro['abono'] ?? 0;

            DB::beginTransaction();

        try {
            //Creamos el registro aqui ponemos los campos que van en la base de datos
            $crearRegistro = Registro::create([
                'nombre' => $validarRegistro['cliente'],
                'descripcion' => $validarRegistro['producto'],
                'valor' => $validarRegistro['precio'],
                'id_estado' => $validarRegistro['formaPago'],
            ]);

            //Creamos el abono en la tabla abonos con el id del registro creado
            if ($abono > 0 || $crearRegistro['formaPago'] == 2) {
                //Usamos la relacion para crear el abono, asignando automaticamente el id_Registro
                $crearRegistro->abonos()->create([
                    'valor' => $abono,
                ]);
            }

            DB::commit();

            return redirect()->route('registros')->with('success','Registro creado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info('Error en el registro' . $e->getMessage());
            return back()->withErrors(['error' => 'Error en el registro']);
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
        if ($request->ajax()) {
            DB::beginTransaction();

            try {
                $registro = Registro::find($request->id_registro);
                $abono = Abono::find($request->id_abono);

                if (!$abono) {
                    DB::rollBack();
                    return response()->json([
                        'code' => 404,
                        'msg' => 'error',
                        'message' => 'Abono no encontrado'
                    ]);
                }

                if ($request->abono <= 0 || $request->abono > $registro->restante) {
                    DB::rollBack();
                    return response()->json([
                        'code' => 422,
                        'msg' => 'error',
                        'message' => 'El valor del abono debe ser mayor que 0 y menor o igual al restante',
                    ]);
                }

                $abono->valor += $request->abono;
                $abono->save();

                DB::commit();

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
                'message' => 'No se pudo encontrar el registro',
            ]);
        }
    }
}
