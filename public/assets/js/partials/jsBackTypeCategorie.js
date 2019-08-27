$(document).ready(function() {
    /* Méta-titre automatique */
    $('#typecategorie_nom').on('keyup', function(){
        if($('body').hasClass('new')){
            titre = $(this).val();
            $('#typecategorie_SEO_metaTitre').val(titre);
        }
    });
});