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

    if($('body').hasClass('new') || $('body').hasClass('edit')){
        scoreSEOChargement($('#page_active_SEO_url'), 75);
        scoreSEOChargement($('#page_active_SEO_metaTitre'), 65);
        scoreSEOChargement($('#page_active_SEO_metaDescription'), 150);
    }

    $('#page_active_SEO_url').on('keyup', {
        champ: $('#page_active_SEO_url'),
        score: 75
    }, scoreSEOLive);
    $('#page_active_SEO_metaTitre').on('keyup', {
        champ: $('#page_active_SEO_metaTitre'),
        score: 65
    }, scoreSEOLive);
    $('#page_active_SEO_metaDescription').on('keyup', {
        champ: $('#page_active_SEO_metaDescription'),
        score: 150
    }, scoreSEOLive);

    //Aperçu Google
    $('#page_active_SEO_url, #page_active_SEO_metaTitre, #page_active_SEO_metaDescription').on('keyup', function(){
        identifiant = $(this).attr('id').split('_').pop();
        $('.listeSEO-apercu .'+identifiant).html($(this).val());
    });

    //Réinitialisation onglet SEO d'une page
    $('#page_active_SEO > .raz').click(function(){
        if(!$(this).hasClass('ok')){
            $('#page_active_SEO_url').val(str2url($('#page_active_titre').val()).substr(0, 75));
            scoreSEOChargement($('#page_active_SEO_url'), 75);

            $('#page_active_SEO_metaTitre').val($('#page_active_titre').val().substr(0, 65));
            scoreSEOChargement($('#page_active_SEO_metaTitre'), 65);

            $('#page_active_SEO_metaDescription').val($('#page_active_titre').val().substr(0, 150));
            scoreSEOChargement($('#page_active_SEO_metaDescription'), 150);

            saveCloseFormulaire();

            $(this).addClass('ok');

            $('#page_active_SEO_url, #page_active_SEO_metaTitre, #page_active_SEO_metaDescription').each(function(){
                identifiant = $(this).attr('id').split('_').pop();
                $('.listeSEO-apercu .'+identifiant).html($(this).val());
            });
        }
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

    //Si titre dans le menu, méta-titre, url ou méta-description vide en perdant le focus, on reprend le titre
    $('#page_active_titreMenu').focusout(function(){
        if($(this).val() === ''){
            $(this).val($('#page_active_titre').val());
        }
    });

    $('#page_active_SEO_url').focusout(function(){
        if($(this).val() === ''){
            $(this).val(str2url($('#page_active_titre').val()));
            scoreSEOChargement($(this), 75);
        }
    });

    $('#page_active_SEO_metaTitre').focusout(function(){
        if($(this).val() === ''){
            $(this).val($('#page_active_titre').val());
            scoreSEOChargement($(this), 65);
        }
    });

    $('#page_active_SEO_metaDescription').focusout(function(){
        if($(this).val() === ''){
            $(this).val($('#page_active_titre').val());
            scoreSEOChargement($(this), 150);
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