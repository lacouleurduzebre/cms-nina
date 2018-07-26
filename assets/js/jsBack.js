$(document).ready(function(){
    /* Toggle colonne de gauche */
    if($.cookie('full') === 'on'){
        $('body').addClass('full');
    }else{
        $('body').addClass('notFull');
    }

    $('#btnArbo').click(function(e){
        e.preventDefault();
        $('body').toggleClass('full');
        $('body').toggleClass('notFull');
        $.cookie('full') === 'on' ? $.cookie('full', 'off') : $.cookie('full', 'on');
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
        if(!$('body.front').hasClass('connected') && !$('body.easyadmin').hasClass('edit-page_active')) {
            return false;
        }
        if($('body').hasClass('front')){
            idPage = $('main.page').attr('id');
        }else if($('body').hasClass('easyadmin')){
            idPage = $('form').attr('data-entity-id');
        }
        $('.page#'+idPage).parent('a').addClass('page-active');
        $('.page#'+idPage).parents('.jstree').find('li[id$="_1"] > a').addClass('page-active');
    };

    $('.sidebar-menus div[id^="menu"]').on('ready.jstree open_node.jstree move_node.jstree', surbrillancePageActive);

    /* Affichage du formulaire en fonction du type du module */
    $('#edit-page_active-form').on('change', 'select[id^="page_active_modules"]', function(){
        type = $(this).val();
        id = $(this).attr('id');
        idModule = $(this).closest('.field-module').children('label').html();
        $(this).closest('div').append('<i class="fas fa-sync fa-spin module"></i>');

        $.ajax({
            url: Routing.generate('ajouterModule'),
            method: "post",
            data: {type: type}
        })
            .done(function(data){
                $('#'+id).next('svg').attr('class', 'fas fa-check').css('opacity', 0);
                $('#'+id).closest('div[id^="page_active"]').find('div[id$="contenu"]').append(data);
                // $('#'+id).prop('disabled', 'disabled');
                $('#page_active_modules_'+idModule+'_contenu').find('label').each(function(){
                    idLabel = $(this).attr('for');
                    champ = idLabel.substring(idLabel.indexOf('_') + 1);
                    $(this).attr('for', 'page_active[modules]['+idModule+'][contenu]['+champ+']');
                    $(this).next('*').attr('name', 'page_active[modules]['+idModule+'][contenu]['+champ+']');
                });
                // TinyMCE
                tinymce.remove();
                tinymce.init({
                    selector: "textarea",
                    theme: "modern",
                    height: 300,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
                        "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
                    ],
                    relative_urls: false,
                    menubar: false,

                    filemanager_title:"Médiathèque",
                    external_filemanager_path:"/filemanager/",
                    external_plugins: { "filemanager" : "/filemanager/plugin.min.js"},

                    extended_valid_elements: 'i[class]',
                    image_advtab: true,
                    toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | formatselect",
                    toolbar2: "| responsivefilemanager | image | media | link unlink anchor | preview | code"
                });
            })
            .fail(function(){
                $('#'+id).next('svg').attr('class', 'fas fa-times').css('opacity', 0);
            });
    });

    /* Changement du h1 lors de l'édition d'une page */
    $('body.edit-page_active h1').html('Page : '+$('#page_active_titre').val());

    $('#page_active_titre').on('keyup', function(){
        $('body.edit-page_active h1').html('Page : '+$(this).val());
    });
});