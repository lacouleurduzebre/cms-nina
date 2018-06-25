$(document).ready(function(){
    /* Afficher / masquer l'arborescence dans la colonne de gauche */
    $('#btnArbo').click(function(){
        $(this).parent('.arbo-titre').toggleClass('active').toggleClass('inactive');
        $(this).parents('header').siblings('div').slideToggle();
    });

    /* Pop-up pour confirmer une suppression */
    $('.action-delete').click(function(e){
        e.preventDefault();
        $('#modal-delete').css('display', 'flex');
    });
});