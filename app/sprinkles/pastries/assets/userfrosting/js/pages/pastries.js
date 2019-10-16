
function reloadOnFormSuccess() {

}

$(document).ready(function() {

// Recargar página cuando el modal termina exitosamente
$("body").on('renderSuccess.ufModal', function() {
    var modal = $(this).ufModal('getModal');
    var form = modal.find('.js-form');

    form.ufForm().on("submitSuccess.ufForm", function() {
        window.location.reload();
    });
});

$(".agregar-postre").click(function(e) {
    e.preventDefault();

    $("body").ufModal({
        sourceUrl: site.uri.public + "/modals/pastries/add",
        msgTarget: $("#alerts-page")
    });
});

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

});