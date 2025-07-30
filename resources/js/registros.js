document.addEventListener("DOMContentLoaded", function () {
    let formaPago = document.getElementById('formaPago');
    let campoAbono = document.getElementById('campoAbono');
    let labelAbono = document.getElementById('labelAbono');
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
     * El ready para ejecutar las funciones AJAX
     */
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        actualizarAbono();
    });

    function actualizarAbono() {
        $("#tabla_registros tbody").on('click', '.actualizarAbono', function() {
            let id_registro = $(this).attr('data-id_registro');
            let id_abono = $(this).attr('data-id_abono');
            let valor_abono = $(this).attr('data-valor_abono');
            console.log("ID REGISTRO " , id_registro);
            console.log("ID ABONO ",  id_abono);
            console.log("VALOR ABONO " , valor_abono);
            mostrarAbono(id_registro,  id_abono, valor_abono);
        });
    }

    /**
     * Ajax para abonar
     */
    async function mostrarAbono(id_registro, id_abono, valor_abono) {
        const result = await Swal.fire({
            title: "Agregar abono",
            input: "number",
            inputLabel: "Valor del abono",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonText: "Guardar",
            showLoaderOnConfirm: true,
            preConfirm: (abono) => {
                if (!abono || abono <= 0) {
                    Swal.showValidationMessage('Ingresa un valor valido');
                }
                return abono;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const abonoIngresado = result.value; //Valor del abono ingresado
                console.log("Abono ingresado", abonoIngresado);

                //Enviamos ajax
                $.ajax({
                    type: "POST",
                    url: '/editar/' + id_registro,
                    data: {
                        id_registro: id_registro,
                        id_abono: id_abono,
                        valor_abono: valor_abono,
                        abono: abonoIngresado,
                    },
                    dataType: 'json',
                    success: function (response) {
                        if(response.code === 200) {
                            Swal.fire({
                                icon: "success",
                                title: "Abono agregado",
                                text: "Abonado: " + abonoIngresado,
                                confirmButtonText: "Listo",
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Advertencia',
                                text: response.message
                            });
                        }
                    },
                    error: function (data) {
                        let errorJson = JSON.parse(data.responseText);
                        Swal.fire({
                            icon: "error",
                            title: "Error al guardar el abono",
                            text: errorJson.message,
                        });
                    }
                });
            } else {

            }
        });
    }
});
