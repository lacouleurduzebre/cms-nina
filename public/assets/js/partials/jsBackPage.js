$(document).ready(function() {
    /* Changement du h1 lors de l'édition d'une page */
    if($('#page_active_titre').length > 0){
        $('h1').html('Page : '+$('#page_active_titre').val());
    }

    $('#page_active_titre').on('keyup', function(){
        $('h1').html('Page : '+$(this).val());
    });

    /* Méta-titre automatique */
    $('#page_active_titre').on('keyup', function(){
        if($('body').hasClass('new') || $('#page_active_titreMenu').val() === ''){
            titre = $(this).val();
            $('#page_active_titreMenu').val(titre);
            if($('body').hasClass('new')){
                $('#page_active_SEO_metaTitre').val(titre);
                $('#page_active_SEO_metaDescription').val(titre);
            }
        }
    });

    /* Traduction des pages */
    langue = $('#page_active_langue').val();
    $('label[for="page_active_traductions_'+langue+'"]').hide().next('select').hide();

    $('#page_active_langue').change(function(){
        langue = $('#page_active_langue').val();
        $('label[for^="page_active_traductions_"]').show().next('select').show();
        $('label[for="page_active_traductions_'+langue+'"]').hide().next('select').hide();
    });

    //Apercu mobile et tablette
        //Fermeture
    $('#conteneurApercu').on('click', '.conteneurApercu-fermeture', function(){
        $('#conteneurApercu').removeClass('paysage').hide().empty();
    });

    insertionIframe = function(type, largeur, hauteur){
        $('#conteneurApercu').show().append('<span id="rotation-'+type+'" class="fa-stack" onclick="$(this).closest(\'div\').toggleClass(\'paysage\')"><i class="fas fa-sync fa-stack-2x"></i><i class="fas fa-mobile fa-stack-1x"></i></span><div class="conteneurApercu-fermeture"><i class="fas fa-times"></i></div><div class="fond-'+type+'"><iframe id="iframeApercu" width="'+largeur+'" height="'+hauteur+'" src="'+$('.action-voir').attr("href")+'" onload="$(this).contents().find(\'.main-header\').hide(); $(this).contents().find(\'.main-sidebar\').hide(); $(this).contents().find(\'body\').removeClass(\'connected notFull\').addClass(\'full\');"></iframe></div>');
    };

    $('#conteneurApercu').on('load', '#iframeApercu', function(){
        $(this).contents().find('.main-header').hide();
        $(this).contents().find('.main-sidebar').hide();
        $(this).contents().find('body').removeClass('connected notFull').addClass('full');
    });

        //Mobile
    $('#apercuMobile').click(function(){
        insertionIframe('mobile', 350, 650);
    });

        //Tablette
    $('#apercuTablette').click(function(){
        insertionIframe('tablette', 770, 1030);
    });

    //Si titre dans le menu vide en perdant le focus, on reprend le titre
    $('#page_active_titreMenu').focusout(function(){
        if($(this).val() === ''){
            $(this).val($('#page_active_titre').val());
        }
    });

    //Confirmation fermeture page après avoir cliqué sur "créer une traduction"
    if($('body').hasClass('new')){
        get = parseURLParams(location.href);
        if(get.confirmation){
            if(get.confirmation[0] === 'oui'){
                saveCloseFormulaire();
            }
        }
    }
});