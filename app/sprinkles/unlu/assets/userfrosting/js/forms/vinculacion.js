$(function() {
    $(".agregar-integrante").click(function() {
        $("#integrantes").append($(".controles_integrante").first().clone());
    });

    $(".quitar-integrante").click(function() {
        if ($("#integrantes .controles_integrante").length > 1) {
            $("#integrantes").find(".controles_integrante").last().remove();
        }
    });
});
