$(function () {
    $('#accesspanel').on('submit', function (e) {
        e.preventDefault();

        const btn = $("#go");
        const header = $("#litheader");

        // Reset estilos anteriores
        header.removeClass("poweron");
        btn.removeClass("denied").val("Inicializando...");

        // Efecto de encendido
        header.addClass("poweron");

        // Espera 1 segundo y luego envía el formulario
        setTimeout(() => {
            this.submit();
        }, 1000);
    });
});
