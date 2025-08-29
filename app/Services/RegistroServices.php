<?php

namespace App\Services;

use App\Exceptions\AbonoMayorAlTotalException;
use App\Exceptions\AbonoNegativoException;
use App\Exceptions\AbonoNoEncontradoException;
use App\Models\Registro;
use App\Repositories\RegistroRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroServices
{
    /**
     * @var RegistroRepository
     */
    protected RegistroRepository $registroRepository;

    /**
     * @param RegistroRepository $registroRepository
     */
    public function __construct(RegistroRepository $registroRepository)
    {
        $this->registroRepository = $registroRepository;
    }

    /**
     * Servicio encargado de mostrar la vista
     *
     * @param Request $request
     * @return string
     */
    public function mostrarRegistroService(Request $request) {
        //Agarramos el celular del cliente
        $celular = $request->get('celular');

        //Variable para guardar la deuda del cliente
        $deudaCliente = $this->calcularDeudaCliente($celular);

        //Aplicamos un filtro con when, tomamos el celular y lo ponemos en funcion
        $registros = $this->registroRepository->obtenerRegistrosConFiltro($celular, 7);

        //Obtenemos los abonos de la base de datos
        $abonos = $this->registroRepository->obtenerAbonos();
        $dineroTotal = $this->calcularTotalService();

        //Miramos si la respuesta es AJAX
        if ($request->ajax() || $request->wantsJson()) {
            //Si es AJAX retornamos el html
            //$view = view('vista_registro.tabla_registros', compact('registros', 'abonos', 'dineroTotal'));

            return response()->json([
                'html' => $this->renderTabla($registros, $abonos, $dineroTotal),
                'deudaCliente' => $deudaCliente,
            ]);
        }

        //Si no es AJAX entonces retornamos la vista normal
        return view('vista_registro.main', compact('registros', 'abonos', 'dineroTotal'));
    }

    /**
     * @param $registros
     * @param $abonos
     * @param $dineroTotal
     * @return string
     */
    public function renderTabla($registros, $abonos, $dineroTotal): string
    {
        return view('vista_registro.tabla_registros', compact('registros', 'abonos', 'dineroTotal'))->render();
    }

    /**
     * @param $celular
     * @return array|void
     */
    public function calcularDeudaCliente($celular) : ?array
    {
        /**
         * Este if se encarga de que solo cuando se hayan ingresado
         * los 10 digitos de un celular haga el cálculo de cuanto deben
         * con el restante que tienen
         */
        if($celular && strlen($celular) === 10) {
            //Buscamos el cliente por celular
            $clienteEncontrado = $this->registroRepository->buscarCliente($celular);

            if($clienteEncontrado->isNotEmpty()) {
                $nombreCliente = $clienteEncontrado->first()->nombre;
                $deuda = $clienteEncontrado->sum('restante');

                /**
                 * Guardamos el nombre y la deuda total en este arreglo
                 * y lo ponemos en el return JSON de esta funcion para luego
                 * usarlo en el JS, ya que lo retornamos como JSON
                 */
                return [
                    'nombre' => $nombreCliente,
                    'deuda' => $deuda,
                ];
            }
        }

        //Si no encuentra el cliente retornamos null
        return null;
    }

    /**
     * @param array $validarRegistro
     * @return Registro
     * @throws AbonoMayorAlTotalException
     * @throws AbonoNegativoException
     *
     * Servicio encargado de crear el registro donde le pasamos el $request->validated()
     * Y se toman los campos desde ahi
     */
    public function crearRegistroService(array $validarRegistro): Registro {
        return DB::transaction(function () use ($validarRegistro) {
            //Si el abono es nulo lo ponemos como cero
            $abono = (float) ($validarRegistro['abono'] ?? 0);

            /**
             * Guardamos cantidad y precio para operar
             */
            $cantidad = $validarRegistro['cantidad'];
            $precio = $validarRegistro['precio'];

            //Calculamos el precio total con cantidad y valor
            $precioTotal = $cantidad * $precio;

            //Como es un servicio lanzamos una excepcion para que lo tome el catch del controlador
            if ($abono > $precioTotal) throw new AbonoMayorAlTotalException($precioTotal);
            if ($abono < 0) throw new AbonoNegativoException();

            //Creamos el registro aqui ponemos los campos que van en la base de datos
            $registro = $this->registroRepository->crearRegistroRepo($validarRegistro, $precioTotal);

            //Creamos el abono en la tabla abonos con el id del registro creado
            $this->registroRepository->crearAbonoRepo($registro, $abono);

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

            //Guardamos los valores del request en variables
            $id_registro = $request->id_registro;
            $id_abono = $request->id_abono;

            //Obtenemos los registros y los abonos por ID con el request
            $registro = $this->registroRepository->obtenerIdRegistro($id_registro);
            $abono = $this->registroRepository->obtenerIdAbonos($id_abono);

            //Si no encuentra el abono lanza una excepcion personalizada
            if (!$abono) {
                throw new AbonoNoEncontradoException();
            }

            //Validar que el valor del abono sea válido
            if ($request->abono <= 0 || $request->abono > $registro->restante) {
                throw new AbonoMayorAlTotalException();
            }

            //Retornamos un arreglo con registro y abono para usarlos en el controlador
            return [
                'registro' => $registro,
                'abono' => $abono,
            ];
        });
    }

    /**
     * @param $id_registro
     * @return bool
     *
     * Eliminar un registro y sus abonos
     * Pasamos el id del registro desde fetch por ajax
     */
    public function eliminarRegistroService($id_registro) : bool
    {
        return DB::transaction(function () use ($id_registro) {
            //Si no encuentra el registro retorna false
            $registro = $this->registroRepository->obtenerIdRegistro($id_registro);
            if (!$registro) {
                return false;
            }
            //Elimina el registro y sus abonos asociados
            return $this->registroRepository->eliminarRegistro($registro);
        });
    }

    /**
     * @return float
     *
     * Caluclar el total del dinero que se tiene
     */
    public function calcularTotalService(): float {
        $registrosTodos = $this->registroRepository->obtenerRegistros();

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
