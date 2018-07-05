$(document).ready(function(){
    /* Toggle colonne de gauche */
    if($.cookie('full') === 'on'){
        $('body').addClass('full');
    }else{
        $('body').addClass('notFull');
    }

    $('#toggleColonneGauche').click(function(){
        $('body').toggleClass('full');
        $('body').toggleClass('notFull');
        $.cookie('full') === 'on' ? $.cookie('full', 'off') : $.cookie('full', 'on');
    });

    /* Afficher / masquer l'arborescence dans la colonne de gauche */
    $('#btnArbo').click(function(){
        $(this).parent('.arbo-titre').toggleClass('active').toggleClass('inactive');
        $(this).parents('header').siblings('div').slideToggle();
    });

    /* Toggle des menus de l'arborescence */
    $('.menuToggle').click(function(){
        //$(".menuToggle").not(this).addClass('inactif');
        //$(".menuToggle").not(this).next('div').slideUp();
        $(this).toggleClass('inactif');
        $(this).next('div').slideToggle();
    });

    /* Pop-up pour confirmer une suppression */
    $('.action-delete').click(function(e){
        e.preventDefault();
        $('#modal-delete').css('display', 'flex');
    });

    /* Résolution du problème de textarea vide avec tinymce */
    $('.action-save').click(function(){
        tinyMCE.triggerSave();
    });

    /* URL automatique */
    creationURL = function( event ){
        if($('body').hasClass('new')) {
            var caracteresInterdits = new RegExp('[ \'\"]', 'gi');
            var caracteresInutiles = new RegExp('[()]', 'i');
            var e = new RegExp('[éèêëÉÈÊË]', 'gi');
            var a = new RegExp('[àÀ]', 'gi');
            var u = new RegExp('[ùûÛ]', 'u');
            var o = new RegExp('[ôÔ]', 'u');
            var i = new RegExp('[îïÎÏ]', 'i');
            titreOK = $(this).val()
                .replace(caracteresInterdits, '-')
                .replace(caracteresInutiles, '')
                .replace(e, 'e')
                .replace(a, 'a')
                .replace(u, 'u')
                .replace(o, 'o')
                .replace(i, 'i')
                .toLowerCase();
            url = encodeURIComponent(titreOK);
            $(event.data.cible).val(url);
        }
    };

        /* Pour les pages */
        $('#page_active_titre').on('keyup', {
            cible: '#page_active_SEO_url'
        }, creationURL );

        /* Pour les catégories */
        $('#categorie_nom').on('keyup', {
            cible: '#categorie_url'
        }, creationURL );

        /* Pour les types de catégorie */
        $('#typecategorie_nom').on('keyup', {
            cible: '#typecategorie_url'
        }, creationURL );

    /* Méta-titre automatique */
    $('#page_active_titre').on('keyup', function(){
        if($('body').hasClass('new')){
            titre = $(this).val();
            $('#page_active_SEO_metaTitre').val(titre);
        }
    });

    /* Méta-description automatique */
    $('#page_active_contenu_ifr #tinymce').on('DOMSubtreeModified', function(){
        if($('body').hasClass('new')){
            contenu = $(this).html();
            console.log(contenu);
            $('#page_active_SEO_metaDescription #tinymce').html(contenu);
        }
    });

    /* Page active colorée dans l'arbo */
    surbrillancePageActive = function(){
        console.log('triggered');
        if($('body.front').hasClass('connected')){
            titre = $('h1.titre-page').html();
            $('.jstree-anchor').each(function(){
                if ($(this).text() === titre){
                    $(this).addClass('page-active');
                    $(this).parents('div.jstree').prev('p').addClass('page-active');
                }
            });
        }
    };

    $('.sidebar-menus div[id^="menu"]').on('ready.jstree', surbrillancePageActive);
});