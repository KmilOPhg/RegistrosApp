import swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    let formaPago = document.getElementById('formaPago');
    let campoAbono = document.getElementById('campoAbono');
    let labelAbono = document.getElementById('labelAbono');
    let btnAgregar = document.getElementById('btnAgregar');
    let filtroCelular = '';
    let ultimaPagina = null;
    actualizarAbono();

    /**
     * Miramos que forma de pago es si es credito o si es contado
     * Miramos si los campos existen
     * Si es credito habilitamos el campo de abono
     * Si es contado no se habilita
     */
    if(!formaPago || !campoAbono || !labelAbono) {
        console.error("Error: No se encontraron todos los elementos necesarios para la lógica del abono.");
        return;
    }
    function ponerAbono() {
        //Pasar los valores a numero
        const formaDePagoSeleccionada = parseInt(formaPago.value, 10)

        if (formaDePagoSeleccionada === 1) {
            console.log("pago contadosss");

            //Ocultamos con hidden que esta en el css
            campoAbono.style.display = "none";
            labelAbono.style.display = "none";
        } else {
            console.log("pago credito");

            //Eliminamos la ocultacion
            campoAbono.style.display = "inline";
            labelAbono.style.display = "inline";
        }

    }
    //Ejecutar la funcion al iniciar la pagina
    ponerAbono();
    //Cuando el valor del selector de forma de pago cambie, ejecutar la función
    formaPago.addEventListener('change', ponerAbono);

    /**
     * Ejecutar las funciones AJAX
     */
    function actualizarAbono() {
        //Seleccionamos la tabla y capturamos el click al boton
        //En este caso se selecciona el contenedor de la tabla que encierra toda la tabla en el main.blade.php
        document.querySelector('#contenedor_tabla').addEventListener('click',  function(event) {

            //Buscamos si algún elemeno clicado tiene la clase btnAbonar
            const btnAbonar = event.target.closest('.btnAbonar');
            if(btnAbonar) { //Si encuentra el boton

                //Obtenemos los valores del html pasados por el boton y los pasamos por argumentos a la funcion
                let id_registro = btnAbonar.getAttribute('data-id_registro');
                let id_abono = btnAbonar.getAttribute('data-id_abono');
                let valor_abono = btnAbonar.getAttribute('data-valor_abono');

                //No me molestes, ya sé que no estoy haciendo nada con esta promesa
                // noinspection JSIgnoredPromiseFromCall
                mostrarAbono(id_registro, id_abono, valor_abono);
            } else {
                /*Swal.fire({
                   icon: 'error',
                   title: 'Error',
                   text: 'Hubo un error inesperado al presionar el boton',
                });*/
            }
        });
    }

    /**
     * Ajax para abonar
     */
    async function mostrarAbono(id_registro, id_abono, valor_abono) {
        //Alerta SwalFire
        const result = await Swal.fire({
            title: "Agregar abono",
            input: "number",
            inputLabel: "Valor del abono",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonText: "Guardar",
            showLoaderOnConfirm: true,

            //Al darle confirmar hacer una validacion
            preConfirm: (abono) => {
                if (!abono || abono <= 0) {
                    Swal.showValidationMessage('Ingresa un valor valido');
                }
                return abono;
            }
            //Entonces inicia el resultado si lo confirma
        }).then(async (result) => {
            if (result.isConfirmed) {
                const abonoIngresado = result.value; //Valor del abono ingresado
                console.log("Abono ingresado", abonoIngresado);

                //Enviamos ajax
                try {
                    //Espera a que todo esto se ejecute con await
                    const response = await fetch(`/editar/${id_registro}`, {
                        method: "POST",
                        headers: { "Content-Type": "application/json",
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
                        body: JSON.stringify({
                            id_registro: id_registro,
                            id_abono: id_abono,
                            valor_abono: valor_abono,
                            abono:  abonoIngresado,
                        })
                    });

                    //Espera a que devuelva el json con await
                    const data = await response.json();

                    if(data.code === 200) {
                        Swal.fire({
                            icon: "success",
                            title: "Abono agregado",
                            text: "Abonado: " + abonoIngresado,
                            confirmButtonText: "Listo",
                        }).then(() =>{
                            //Entonces vamos a la vista dejando solo la ultima pagina
                            if(ultimaPagina) {
                                pasarPagina(ultimaPagina);
                            }
                        });
                    } else {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: 'No puedes abonar mas de lo que te deben'
                        });
                    }
                } catch (error) {
                    console.log('Hubo un error al cargar el resultado: ' , error);
                    await Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Hubo un error al actualizar la tabla '+ error.message,
                    });
                }
            } else {
                /*await swal.fire({
                   icon: "info",
                   title: "Cancelado",
                   text: "Cancelaste el abono"
                });*/
            }
        });
    }

    /**
     * Funcion para pasar pagina AJAX
     * @returns {Promise<void>}
     */
    async function pasarPagina(url) {
        //Agarramos la URL que le pasamos por parametro
        ultimaPagina = url;
        //Si contiene ? o si no, para aplicar el filtro bien en la URL
        if(url.includes("?")) {
            ultimaPagina += `&celular=${filtroCelular}`;
        } else {
            ultimaPagina = url + `?celular=${filtroCelular}`;
        }

        //Enviar AJAX
        try {
            //Usamos fetch con GET para que laravel reconozca que es AJAX
            const response = await fetch(ultimaPagina, {
                method: "GET",
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            //Esperamos una respuesta json
            const data = await response.json();

            //Actualizamos la tabla
            document.querySelector('#contenedor_tabla').innerHTML = data.html;

            //Calculamos la deuda de un cliente
            caluclarDeudaCliente(data);
        } catch (error) {
            console.error("Error al cargar la página:", error);
            await swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Hubo un error al cargar la tabla.",
            })
        }
    }

    /**
     * Fumcion para calcular la deuda de un cliente
     * @param data
     */
    function caluclarDeudaCliente(data) {
        //Agarramos el h4 del HTML para insertar el texto de lo que debe
        const infoClienteDeuda = document.querySelector('#infoCliente');

        //De la data que recibimos, agarramos del backend el nombre y la deuda que pasamos por JSON
        //Viene de la funcion mostrarRegistros()
        if(data.deudaCliente) {
            //Accedemos a los datos, nombre y deuda para luego meterlos en el h4 del HTML
            const nombre = data.deudaCliente.nombre;
            const deuda = data.deudaCliente.deuda.toLocaleString();
            infoClienteDeuda.textContent = `Dinero que debe ${nombre}: ${deuda}`;
        } else {
            infoClienteDeuda.textContent = ``;
        }
    }

    /**
     * Listener para manejar la paginacion con AJAX
     */
    document.addEventListener('click', function (e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            const url = e.target.href;
            //No me molestes, ya sé que no estoy haciendo nada con esta promesa
            // noinspection JSIgnoredPromiseFromCall
            pasarPagina(url);
        }
    });

    /**
     * Listener para manejar el filtro junto a la paginacion
     */
    document.addEventListener('input', function (e) {
        e.preventDefault();
        //Si es el campo de filtro entonces aplica el filtro
        const esFiltro = e.target.id === 'filtro';
        if (esFiltro) {
            //Guardamos el valor ingresado en ese campo en la variable global
            filtroCelular = e.target.value;
            //No me molestes, ya sé que no estoy haciendo nada con esta promesa
            //Pasamos la URL directa a la funcion pasar pagina
            // noinspection JSIgnoredPromiseFromCall
            pasarPagina('/registros');
        }
    });

    /**
     * Listener para el loading del boton
     */
    document.addEventListener('submit', async function (e) {
        e.preventDefault();

        //Agarramos el id del formulario
        if(e.target.id === 'formAgregarCliente') {
            //Clase css para el spinner
            btnAgregar.classList.add('loading');
            btnAgregar.disabled = true;

            try {
                //Agregamos una variable formData que almacena los datos del formulario
                const formData = new FormData(e.target)

                //Una variable solo para targetear el formulario
                const formTarget = e.target;

                //Guardamos en otra variable formulario lo que obtuvimos de formData
                const formulario = Object.fromEntries(formData.entries());

                //Enviamos por fecth a la ruta que hace la funcion de agregar en laravel
                const response = await fetch('/agregar', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    //Como el metodo es POST tenemos que pasarle un JSON y ese JSON debe tener los datos del formulario
                    body: JSON.stringify(formulario)
                });

                const data = await response.json();

                if(data.code === 200) {
                    swal.fire({
                        icon: "success",
                        title: "Agregado correctamente",
                        text: "Cliente agregado correctamente",
                    })
                    //Entonces vamos a la vista dejando solo la ultima pagina
                    if(ultimaPagina) {
                        await pasarPagina(ultimaPagina);
                    }
                    btnAgregar.classList.remove('loading');
                    btnAgregar.disabled = false;
                    formTarget.reset();
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al agregar cliente else'
                    });
                    btnAgregar.classList.remove('loading');
                    btnAgregar.disabled = false;

                }
            } catch (error) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al agregar cliente catch'
                });
                btnAgregar.classList.remove('loading');
                btnAgregar.disabled = false;
            }
        }
    });
});
