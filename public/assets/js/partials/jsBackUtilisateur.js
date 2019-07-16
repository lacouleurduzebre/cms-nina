$(document).ready(function() {
    //Modif du mot de passe
    $('#modifMDP').fancybox({
        type: 'iframe',
        minHeight: '600'
    });

    /* Changement couleur BO */
    couleur = $('#utilisateur_couleurBO').val();
    
    $('#utilisateur_couleurBO').on('change', function(){
        nouvelleCouleur = $(this).val();
        $('body').removeClass(couleur).addClass(nouvelleCouleur);
        couleur = nouvelleCouleur;
    });
});