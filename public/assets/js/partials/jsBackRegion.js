$(document).ready(function() {
    /* Changement du h1 lors de l'édition d'une région */
    if($('#region_nom').length > 0){
        $('h1').html('Région <i>'+$('#region_nom').val()+'</i>');
    }

    $('#region_nom').on('keyup', function(){
        $('h1').html('Région <i>'+$(this).val()+'</i>');
    });
});