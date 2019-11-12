function getSelectIntegrante() {
    var $html = $("#input-usuario").clone();
    return $html.html();
}

$(function() {
    $(".agregar-integrante").click(function() {
        $("#integrantes").append($(".input-integrante").first().clone());
    });

    $(".quitar-integrante").click(function() {
        if ($("#integrantes .input-integrante").length > 1) {
            $("#integrantes").find(".input-integrante").last().remove();
        }
    });
});
