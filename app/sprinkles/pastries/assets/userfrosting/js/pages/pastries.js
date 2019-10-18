$(document).ready(function() {

// Recargar página cuando el modal termina exitosamente
$("body").on('renderSuccess.ufModal', function() {
    var modal = $(this).ufModal('getModal');
    var form = modal.find('.js-form');

    form.ufForm().on("submitSuccess.ufForm", function() {
        window.location.reload();
    });
});

// Modal para agregar un postre
$(".agregar-postre").click(function(e) {
    e.preventDefault();

    $("body").ufModal({
        sourceUrl: site.uri.public + "/modals/pastries/add",
        msgTarget: $("#alerts-page")
    });
});

// Modal para borrar un postre
$(".borrar-postre").click(function(e) {
    e.preventDefault();

    $("body").ufModal({
        sourceUrl: site.uri.public + "/modals/pastries/delete",
        ajaxParams: {
            name: $(this).data('name')
        },
        msgTarget: $("#alerts-page")
    });
});

// Modal para editar un postre
$(".editar-postre").click(function(e) {
    e.preventDefault();

    $("body").ufModal({
        sourceUrl: site.uri.public + "/modals/pastries/edit",
        ajaxParams: {
            name: $(this).data('name')
        },
        msgTarget: $("#alerts-page")
    });
});

});