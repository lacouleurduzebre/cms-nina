$(document).ready(function(){
    /* Initialisation TinyMCE */
    tinymce.init({
        selector: "textarea:not('.notTinymce')",
        language: "fr_FR",
        theme: "modern",
        height: 300,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
            "table contextmenu directionality emoticons paste textcolor responsivefilemanager code imagetools"
        ],
        relative_urls: false,
        menubar: false,

        filemanager_title:"Médiathèque",
        external_filemanager_path:"/filemanager/",
        external_plugins: { "filemanager" : "/filemanager/plugin.min.js"},

        extended_valid_elements: 'i[class]',
        block_formats: 'Paragraphe=p;Titre h2=h2;Titre h3=h3;Titre h4=h4;Titre h5=h5;Titre h6=h6',
        image_advtab: true,
        toolbar1: "formatselect | image | media | link unlink | copy paste pastetext | bold italic underline | alignleft aligncenter alignright | bullist numlist | code | undo redo",

        init_instance_callback: function (editor) {
            editor.on('change', function (e) {
                $('.formulaire-actions-enregistrer').attr("disabled", false);
            });
        }
    });

    /* Pop-up pour confirmer une suppression */
    $('.action-delete').click(function(e){
        e.preventDefault();
        $('#modal-delete').css('display', 'flex');
    });

    /* Résolution du problème de textarea vide avec tinymce */
    $('.formulaire-actions-enregistrer').click(function(){
        tinyMCE.triggerSave();
    });

    $('.action-save').click(function(){
        tinyMCE.triggerSave();
    });

    /* URL automatique */
    creationURL = function( event ){
        if($('body').hasClass('new')) {
            var e = new RegExp('[éèêëÉÈÊË]', 'gi');
            var a = new RegExp('[àÀ]', 'gi');
            var u = new RegExp('[ùûÛ]', 'u');
            var o = new RegExp('[ôÔ]', 'u');
            var i = new RegExp('[îïÎÏ]', 'i');
            titreOK = $(this).val()
                .replace(e, 'e')
                .replace(a, 'a')
                .replace(u, 'u')
                .replace(o, 'o')
                .replace(i, 'i')
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '') // Trim - from end of text
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

    /* Affichage du formulaire en fonction du type du bloc */
    $('form[id$="page_active-form"]').on('change', 'select[id^="page_active_blocs"]', function(){
        type = $(this).val();
        typeFormalise = $(this).find('option:selected').html();
        id = $(this).attr('id');
        idBloc = $(this).closest('.field-bloc').children('label').html();
        $(this).closest('div').append('<i class="fas fa-sync fa-spin bloc"></i>');

        $.ajax({
            url: Routing.generate('ajouterBloc'),
            method: "post",
            data: {type: type}
        })
            .done(function(data){
                $('#'+id).closest('div').find('svg').hide();
                $('#'+id).closest('div[id^="page_active"]').find('div[id$="contenu"]').append(data);
                $('#page_active_blocs_'+idBloc+'_contenu').find('label').each(function(){
                    idLabel = $(this).attr('for');
                    champ = idLabel.substring(idLabel.indexOf('_') + 1);
                    $(this).attr('for', 'page_active[blocs]['+idBloc+'][contenu]['+champ+']');
                    $(this).next('*').attr('name', 'page_active[blocs]['+idBloc+'][contenu]['+champ+']');
                    $(this).next('*').attr('id', $(this).next('*').attr('id')+idBloc);
                });
                if(type == 'Image'){
                    url = $('#image_image'+idBloc).parent('div').next('div').find('a').attr('href');
                    $('#image_image'+idBloc).parent('div').next('div').find('a').attr('href', url+idBloc);
                    $('.bloc_image_bouton_mediatheque').fancybox({
                        type: 'iframe',
                        minHeight: '600'
                    });
                }
                $('#'+id).closest('div').append('<p class="type-bloc">'+typeFormalise+'</p>');
                $('#'+id).hide().prev('label').hide();
                // TinyMCE
                tinymce.remove();
                tinymce.init({
                    selector: "textarea",
                    language: "fr_FR",
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
                    block_formats: 'Paragraphe=p;Titre h2=h2;Titre h3=h3;Titre h4=h4;Titre h5=h5;Titre h6=h6',
                    image_advtab: true,
                    toolbar1: "formatselect | image | media | link unlink | copy paste pastetext | bold italic underline | alignleft aligncenter alignright | bullist numlist | code | undo redo"
                });
            })
            .fail(function(){
                $('#'+id).closest('div').find('svg').attr('class', 'fas fa-times').css('opacity', 0);
            });
    });

    /* Cacher les select de choix du type de bloc si bloc déjà créé */
    $('.form-group.field-bloc select[id$="type"]').each(function(){
        type = $(this).find('option:selected').html();
        $(this).hide().prev('label').hide().closest('div').append('<p class="type-bloc">'+type+'</p>');
    });

    /* Changement du h1 lors de l'édition d'une page */
    $('body.edit-page_active h1').html('Page : '+$('#page_active_titre').val());

    $('#page_active_titre').on('keyup', function(){
        $('body.edit-page_active h1').html('Page : '+$(this).val());
    });

    /* Changement couleur BO */
    if($('body').hasClass('edit-utilisateur') || $('body').hasClass('new-utilisateur')){
        couleur = $('#utilisateur_couleurBO').val();
    }
    $('#utilisateur_couleurBO').on('change', function(){
       nouvelleCouleur = $(this).val();
       $('body').removeClass(couleur).addClass(nouvelleCouleur);
       couleur = nouvelleCouleur;
    });

    /* Bouton médiathèque */
    if($('body').hasClass('edit') || $('body').hasClass('new')){
        $('.bloc_image_bouton_mediatheque').fancybox({
            type: 'iframe',
            minHeight: '600'
        });

        $('.ouvrirMediatheque').fancybox({
            type: 'iframe',
            minHeight: '600'
        });
    }

    $(document).on('afterClose.fb', function( e, instance, slide ) {
        id = slide.src.substr(slide.src.indexOf('field_id=')+9);
        urlImg = $('#'+id).val();

        if(id === 'utilisateur_imageProfil'){//Image de profil
            $('#'+id).siblings('.apercuImageProfil').find('img').attr('src', urlImg);
        }else if(id === 'configuration_logo'){//Logo du site
            $('#'+id).siblings('.apercuLogo').find('img').attr('src', urlImg);
        }else{//Bloc Image
            $('#'+id).parent('div').next('div').find('img').attr('src', urlImg);
        }

        $(this).find('.formulaire-actions-enregistrer').attr("disabled", false);
    });

    /* Gestion de la position des blocs */
    $("#page_active_blocs").sortable({
        handle: '.drag',
        update: function(event, ui){
            $('.field-bloc').each(function(){
                idBloc = $(this).find('.control-label').html();
                $(this).find('#page_active_blocs_'+idBloc+'_position').val($(this).index());
                $('.formulaire-actions-enregistrer').attr("disabled", false);
            })
        }
    });

    /* Menu blocs */
    $('#page_active_blocs').on('click', '.bloc-menu span', function(){
        $(this).closest('div').toggleClass('actif');
    });

        /* Monter */
        $('#page_active_blocs').on('click', '.monterBloc', function(e){
            e.preventDefault();
            $(this).closest('div').removeClass('actif');

            bloc = $(this).closest('.field-bloc');
            blocPrecedent = bloc.prev('.field-bloc');
            bloc.insertBefore(blocPrecedent);

            $('.field-bloc').each(function(){
                idBloc = $(this).find('.control-label').html();
                $(this).find('#page_active_blocs_'+idBloc+'_position').val($(this).index());
            })
        });

        /* Descendre */
        $('#page_active_blocs').on('click', '.descendreBloc', function(e){
            e.preventDefault();
            $(this).closest('div').removeClass('actif');

            bloc = $(this).closest('.field-bloc');
            blocSuivant = bloc.next('.field-bloc');
            bloc.insertAfter(blocSuivant);

            $('.field-bloc').each(function(){
                idBloc = $(this).find('.control-label').html();
                $(this).find('#page_active_blocs_'+idBloc+'_position').val($(this).index());
            })
        });

        /* Options d'affichage */
        $('#page_active_blocs').on('click', '.optionsAffichage', function(e){
            e.preventDefault();
            $(this).closest('div').removeClass('actif');

            bloc = $(this).closest('.field-bloc');

            bloc.find('.bloc-optionsAffichage').toggleClass('actif');
        });

            /* Fermeture */
        $('#page_active_blocs').on('click', '.bloc-optionsAffichage-fermeture', function(){
           $(this).closest('div').removeClass('actif');
        });

        /* Supprimer */
        $('#page_active_blocs').on('click', '.supprimerBloc', function(e){
            e.preventDefault();
            $(this).closest('div').removeClass('actif');

            bloc = $(this).closest('.field-bloc');
            $('#modal-delete').css('display', 'flex');
            $('#modal-delete').modal({ backdrop: true, keyboard: true })
                .off('click', '#modal-delete-button')
                .on('click', '#modal-delete-button', function () {
                    bloc.slideUp(600, function(){
                        bloc.remove();
                        $('#page_active_titre').keyup();
                    });
                });
        });

    /* Changement de thème */
    $('.theme').click(function(){
       theme = $(this).attr('id');
       $('.messages .loader').show();

        $.ajax({
            url: Routing.generate('modifierTheme'),
            method: "post",
            data: {theme: theme}
        })
            .done(function(){
                $('.messages .loader').hide();
                $('.message-ok').fadeIn().delay(800).fadeOut();
                $('.theme').removeClass('actif');
                $('#'+theme).addClass('actif');
            })
            .fail(function(){
                $('.message-fail').fadeIn().delay(800).fadeOut();
            });
    });

    /* Sauvegarde */
    /* BDD */
    /* Création de dump */
    $('#export-bdd').click(function(){
        $('#export-bdd + .loader').css('display', 'inline-block');
        $.ajax({
            url: Routing.generate('sauvegarderBDD'),
            method: "post"
        })
            .done(function(data){
                $('#export-bdd + .loader').hide();

                timestamp = data.substring(0, data.indexOf('*'));
                date = data.substring(data.indexOf('*')+1);

                $('#dumps-bdd tbody').append('<tr>\n' +
                    '                            <td>'+date+'</td>\n' +
                    '                            <td><a download href="/sauvegardes/bdd/dump'+timestamp+'.sql"><i class="fas fa-file-download"></i></a></td>\n' +
                    '                            <td><a href="#" class="export-bdd-supprimer"><i class="fas fa-trash"></i></a></td>\n' +
                    '                        </tr>');
            })
            .fail(function(){
            });
    });

    /* Suppression de dump */
    $('.sauvegarde').on('click', '.export-bdd-supprimer', function(){
        fichier = $(this).attr('id');
        element = $(this);
        $.ajax({
            url: Routing.generate('supprimerDumps'),
            method: "post",
            data: {fichier: fichier, type: 'bdd'}
        })
            .done(function(){
                element.closest('tr').remove();
            })
    });

    /* Médiathèque */
    /* Création de dump */
    $('#export-mediatheque').click(function(){
        $('#export-mediatheque + .loader').css('display', 'inline-block');
        $.ajax({
            url: Routing.generate('sauvegarderMediatheque'),
            method: "post"
        })
            .done(function(data){
                $('#export-mediatheque + .loader').hide();

                timestamp = data.substring(0, data.indexOf('*'));
                date = data.substring(data.indexOf('*')+1);

                $('#dumps-mediatheque tbody').append('<tr>\n' +
                    '                        <td>'+date+'</td>\n' +
                    '                        <td><a href="/sauvegardes/mediatheque/mediatheque'+timestamp+'.zip"><i class="fas fa-file-download"></i></a></td>\n' +
                    '                        <td><a href="#" class="export-mediatheque-supprimer"><i class="fas fa-trash"></i></a></td>\n' +
                    '                    </tr>');
            })
    });

    /* Suppression de dump */
    $('.sauvegarde').on('click', '.export-mediatheque-supprimer', function(){
        fichier = $(this).attr('id');
        element = $(this);
        $.ajax({
            url: Routing.generate('supprimerDumps'),
            method: "post",
            data: {fichier: fichier, type: 'mediatheque'}
        })
            .done(function(){
                element.closest('tr').remove();
            })
    });

    /* Toggle pour les éléments de tableau en dessous de 768px */
    $('.toggleElementTableau').click(function(){
       $(this).closest('tr').toggleClass('ouvert');
    });

    /* Traduction des pages */
    if($('body').is('[class*="edit-page"]') || $('body').is('[class*="new-page"]')){
        langue = $('#page_active_langue').val();
        $('label[for="page_active_traductions_'+langue+'"]').hide().next('select').hide();
    }

    /* Une seule langue par défaut */
    $('body.list-langue td[data-label="Defaut"] input').click(function(){
       if($(this).is(':checked')){
           $('body.list-langue td[data-label="Defaut"] input').not(this).attr("checked", false);
       }
    });

    /* Activer le bouton d'enregistrement lors de la première modif d'un formulaire */
    $('form').on('change keyup', function(){
        $(this).find('.formulaire-actions-enregistrer').attr("disabled", false);
    });

    /* Fermeture des messages flash */
    $('#flash-messages').on('click', 'svg', function(){
       $(this).closest('div').fadeOut();
    });

    /* Score SEO */
    scoreSEO = function(champ, nbCaracteres, score){
        if(nbCaracteres < (score / 3)){
            champ.siblings('.progression').attr('class', 'progression rouge');
            champ.prev('.nbCaracteres').find('.seo-attention').remove();
        }else if (nbCaracteres > score){
            champ.siblings('.progression').attr('class', 'progression vert');
            if(champ.prev('.nbCaracteres').find('.seo-attention').length < 1){
                champ.prev('.nbCaracteres').append('<div class="seo-attention"><i class="fas fa-exclamation-triangle "></i><span>Si vous dépassez la limite de caractères préconisée, votre texte sera tronqué dans la liste des résultats des moteurs de recherche</span></div>');
            }
        }else if (nbCaracteres >= ((score/3) * 2)){
            champ.siblings('.progression').attr('class', 'progression vert');
            champ.prev('.nbCaracteres').find('.seo-attention').remove();
        }else{
            champ.siblings('.progression').attr('class', 'progression orange');
            champ.prev('.nbCaracteres').find('.seo-attention').remove();
        }
        champ.prev('.nbCaracteres').children('span').html(nbCaracteres);
    };

    scoreSEOChargement = function(champ, score){
        nbCaracteres = champ.val().length;
        scoreSEO(champ, nbCaracteres, score);
    };

    scoreSEOLive = function(event){
        champ = event.data.champ;
        nbCaracteres = event.data.champ.val().length;
        score = event.data.score;
        scoreSEO(champ, nbCaracteres, score);
    };

    if($('body').hasClass('edit-page_active') || $('body').hasClass('new-page_active')){
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
});