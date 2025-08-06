$(function() {
    $('#accesspanel').on('submit', function(e) {
        e.preventDefault();

        const btn = $("#go");
        const header = $("#litheader");

        header.addClass("poweron");
        btn.removeClass("denied").val("Inicializando...");

        setTimeout(() => {
            this.submit();
        }, 1000);
    });
});
