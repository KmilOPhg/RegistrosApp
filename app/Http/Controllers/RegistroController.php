<?php

namespace App\Http\Controllers;

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

            return view('vista_registro.main', compact('registros'));
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
        try {
            DB::beginTransaction();

            //Poner valor por defecto abono
            $abono = $request->input('abono');
            if (is_null($abono)) {
                $abono = 0;
            }


            $validarRegistro = [
                'nombre' => $request->input('cliente'),
                'descripcion' => $request->input('producto'),
                'valor' => $request->input('precio'),
                'abono' => $abono,
                'id_estado' => $request->input('formaPago'),
            ];

            $registro = Registro::create($validarRegistro);
            DB::commit();

            return redirect()->route('registros')->with('success','Registro creado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info('Error en el registro' . $e->getMessage());
            return back()->withErrors(['error' => 'Error en el registro']);
        }
    }
}
