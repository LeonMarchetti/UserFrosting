$(function() {

    // Recargar página cuando el modal termina exitosamente
    $("body").on('renderSuccess.ufModal', function() {
        var modal = $(this).ufModal('getModal');
        var form = modal.find('.js-form');

        form.ufForm().on("submitSuccess.ufForm", function() {
            window.location.reload(true);
        });
    });

    // Modal para agregar un postre
    $(".solicitar-vinculacion").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-vinculacion",
            msgTarget: $("#alerts-page")
        });
    });

    // Modal para agregar un postre
    $(".solicitar-servicio").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-servicio",
            msgTarget: $("#alerts-page")
        });
    });

    // Modal para agregar un postre
    $(".baja-solicitud").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/baja-solicitud",
            msgTarget: $("#alerts-page")
        });
    });

});