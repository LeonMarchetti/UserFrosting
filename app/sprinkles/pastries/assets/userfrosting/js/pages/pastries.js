
function attachPastryForm() {
    $("body").on('renderSuccess.ufModal', function(data) {
        var modal = $(this).ufModal('getModal');
        var form = modal.find('.js-form');

        // Set up any widgets inside the modal
        form.find(".js-select2").select2({
            width: '100%'
        });



        // Set up the form for submission
        form.ufForm({
            validator: page.validators
        }).on("submitSuccess.ufForm", function() {
            // Reload page on success
            window.location.reload();
        });

        // toggleSetPasswordMode(modal, 'link');

        // On submission, submit either the PUT request, or POST for a password reset, depending on the toggle state
        // modal.find("input[name='change_password_mode']").click(function() {
        //     var changePasswordMode = $(this).val();
        //     toggleSetPasswordMode(modal, changePasswordMode);
        // });
    });
}

$(document).ready(function() {

$(".agregar-postre").click(function(e) {
    e.preventDefault();

    $("body").ufModal({
        sourceUrl: site.uri.public + "/modals/pastries/add",
        msgTarget: $("#alerts-page")
    });

    attachPastryForm();
});

});