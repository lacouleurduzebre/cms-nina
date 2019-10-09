$(document).ready(function() {
    /* Méta-titre automatique */
    $('#categorie_nom').on('keyup', function(){
        if($('body').hasClass('new')){
            titre = $(this).val();
            $('#categorie_SEO_metaTitre').val(titre);
            apercuGoogle($('#categorie_SEO_metaTitre'));
            $('#categorie_SEO_metaDescription').val(titre);
            apercuGoogle($('#categorie_SEO_metaDescription'));
        }
    });
});