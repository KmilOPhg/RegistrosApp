import swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    let formaPago = document.getElementById('formaPago');
    let campoAbono = document.getElementById('campoAbono');
    let labelAbono = document.getElementById('labelAbono');
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
                        }).then(() =>{ //Entonces vamos a la vista
                            fetch('/registros', {
                                headers: {
                                    //Detectar AJAX en laravel
                                    'X-Requested-With': 'XMLHttpRequest',
                                }
                            }).then(response => {
                                if(!response.ok) throw new Error('Error en la respuesta' + response.statusText);
                                return response.json(); //Retornamos un json
                            }).then(data => { //Actualizamos todo el contenedor de la tabla que encierra la tabla en el main.blade.php
                                document.querySelector('#contenedor_tabla').innerHTML = data.html;
                            }).catch(e => { //Un catch con Swal para mirar si hay un error
                                console.log('Hubo un error con la carga de la tabla' + e);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Hubo un error al actualizar la tabla.',
                                });
                            });
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
});
