$(document).ready(function() {
    /* Changement du h1 lors de l'édition d'un bloc partagé */
    if ($('#blocpartage_nom').length > 0) {
        $('h1.title').html('Bloc partagé <i>' + $('#blocpartage_nom').val() + '</i>');
    }

    $('#blocpartage_nom').on('keyup', function () {
        $('h1.title').html('Bloc partagé <i>' + $(this).val() + '</i>');
    });
});