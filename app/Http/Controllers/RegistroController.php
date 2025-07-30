<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Registro;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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
    public function mostrarRegistros(){
        try {
            $registros = Registro::with('estado')->get();
            $abonos = Abono::with('registro')->get();

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
}
