$(document).ready(function() {

$(".agregar-postre").click(function() {
    $("body").ufModal({
        // sourceUrl: site.uri.public + "/modals/users/create",
        sourceUrl: site.uri.public + "/modals/pastries/add",
        msgTarget: $("#alerts-page")
    });
});

});