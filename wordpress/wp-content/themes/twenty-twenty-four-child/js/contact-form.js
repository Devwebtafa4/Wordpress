jQuery(document).ready(function($) {
    $('#contact-form').on('submit', function(e) {
        e.preventDefault(); // Empêche l'envoi classique du formulaire

        var name = $('#name').val();
        var email = $('#email').val();
        var message = $('#message').val();
        var nonce = $('#contact_form_nonce').val(); // Récupérer le nonce

        // Envoi de la requête AJAX
        $.ajax({
            url: contact_form_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'submit_contact_form', // L'action qui sera gérée côté PHP
                name: name,
                email: email,
                message: message,
                nonce: nonce
            },
            success: function(response) {
                // Afficher la réponse du serveur
                $('#response').html('<p>' + response + '</p>');
                $('#contact-form')[0].reset(); // Réinitialiser le formulaire
            },
            error: function() {
                $('#response').html('<p>Une erreur est survenue, veuillez réessayer.</p>');
            }
        });
    });
});
