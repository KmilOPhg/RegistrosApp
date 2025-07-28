document.addEventListener("DOMContentLoaded", function () {
    let formaPago = document.getElementById('formaPago');
    let campoAbono = document.getElementById('campoAbono');
    let labelAbono = document.getElementById('labelAbono');

    /**
     * Miramos que forma de pago es si es credito o si es contado
     * Si es credito habilitamos el campo de abono
     * Si es contado no se habilita
     */
    function actualizarAbono() {
        if (parseInt(formaPago.value) === 1) {
            console.log("pago contadosss");
            campoAbono.style.display = 'none';
            labelAbono.style.display = 'none';
        } else {
            console.log("pago credito");
            campoAbono.style.display = 'inline';
            labelAbono.style.display = 'inline';
        }

    }

    formaPago.addEventListener('change', function() {
        actualizarAbono();
    });

    //Ejecutar la funcion al iniciar la pagina
    actualizarAbono()
});
