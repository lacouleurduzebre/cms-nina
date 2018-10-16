$(document).ready(function(){
    clicEnregistrement = false;
    optionsTinyMCE = {
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
                saveCloseFormulaire();
            });
        }
    };
    //Bouton d'enregistrement / Confirmation fermeture page
    function saveCloseFormulaire(){
        $('.formulaire-actions-enregistrer').attr("disabled", false);
        $(window).bind('beforeunload', function(){
            if(clicEnregistrement = true){
                messageOff = true;
            }
            if(!messageOff){
                return 'Êtes-vous sûr de vouloir quitter cette page ? Des données pourraient ne pas avoir été enregistrées';
            }
        });
    }
    /* Initialisation TinyMCE */
    tinymce.init(optionsTinyMCE);

    /* Pop-up pour confirmer une suppression */
    $('.action-delete').click(function(e){
        e.preventDefault();
        $('#modal-delete').css('display', 'flex');
    });

    /* Résolution du problème de textarea vide avec tinymce */
    $('.formulaire-actions-enregistrer').click(function(){
        clicEnregistrement = true;
        tinyMCE.triggerSave();
    });

    $('.action-save').click(function(){
        tinyMCE.triggerSave();
    });

    /* URL automatique */
    function str2url(str,encoding,ucfirst)
    {
        str = str.toUpperCase();
        str = str.toLowerCase();
        str = str.replace(/[\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g,'a');
        str = str.replace(/[\u00E7]/g,'c');
        str = str.replace(/[\u00E8\u00E9\u00EA\u00EB]/g,'e');
        str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g,'i');
        str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8]/g,'o');
        str = str.replace(/[\u00F9\u00FA\u00FB\u00FC]/g,'u');
        str = str.replace(/[\u00FD\u00FF]/g,'y');
        str = str.replace(/[\u00F1]/g,'n');
        str = str.replace(/[\u0153]/g,'oe');
        str = str.replace(/[\u00E6]/g,'ae');
        str = str.replace(/[\u00DF]/g,'ss');
        str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
        str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
        str = str.replace(/[ ]/g,'-');
        if (ucfirst === 1)
        {
            c = str.charAt(0);
            str = c.toUpperCase()+str.slice(1);
        }
        return str;
    }

    creationURL = function( event ){
        if($('body').hasClass('new')) {
            titre = $(this).val();
            url = str2url(titre, 'UTF-8', true);
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

        /* Modification de l'url */
        $('#page_active_SEO_url').on('keyup', function(){
            urlNonFormattee = $(this).val();
            url = str2url(urlNonFormattee, 'UTF-8', true);
            $(this).val(url);
        });

    /* Méta-titre automatique */
    $('#page_active_titre').on('keyup', function(){
        if($('body').hasClass('new')){
            titre = $(this).val();
            $('#page_active_SEO_metaTitre').val(titre);
            $('#page_active_titreMenu').val(titre);
        }
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

        saveCloseFormulaire();
    });

    /* Gestion de la position des blocs */
    options = {
        handle: '.drag',
        update: function(event, ui){
            $('.field-bloc').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
                saveCloseFormulaire();
            });
            tinymce.remove();
            tinymce.init(optionsTinyMCE);
        }
    };

    $("#page_active_blocs").sortable(options);
    $("#groupeblocs_blocs").sortable(options);

    $(".bloc-slider div[id$='contenu_Slide']").sortable({
        handle: '.dragSlide',
        update: function(event, ui){
            $('.field-slide').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
                saveCloseFormulaire();
            });
        }
    });

    $(".bloc-galerie div[id$='contenu_images']").sortable({
        handle: '.dragGalerie',
        update: function(event, ui){
            $('.field-galerie_image').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
                saveCloseFormulaire();
            });
        }
    });

    $(".bloc-formulaire div[id$='contenu_champs']").sortable({
        handle: '.dragChamp',
        update: function(event, ui){
            $('.field-champ').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
                saveCloseFormulaire();
            });
        }
    });

    /* Menu blocs */
    $('form').on('click', '.bloc-menu span', function(){
        $(this).closest('div').toggleClass('actif');
        $(this).closest('div').find('.suppressionBloc').hide();
    });

        /* Monter */
        $('form').on('click', '.monterBloc', function(e){
            e.preventDefault();
            $(this).closest('div').removeClass('actif');

            bloc = $(this).closest('.field-bloc');
            blocPrecedent = bloc.prev('.field-bloc');
            bloc.insertBefore(blocPrecedent);

            tinymce.remove();
            tinymce.init(optionsTinyMCE);

            $('.field-bloc').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
            });
        });

        /* Descendre */
        $('form').on('click', '.descendreBloc', function(e){
            e.preventDefault();
            $(this).closest('div').removeClass('actif');

            bloc = $(this).closest('.field-bloc');
            blocSuivant = bloc.next('.field-bloc');
            bloc.insertAfter(blocSuivant);

            tinymce.remove();
            tinymce.init(optionsTinyMCE);

            $('.field-bloc').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
            });
        });

        /* Options d'affichage */
        $('form').on('click', '.optionsAffichage', function(e){
            e.preventDefault();
            $(this).closest('div').removeClass('actif');

            bloc = $(this).closest('.field-bloc');

            bloc.find('.bloc-optionsAffichage').toggleClass('actif');
        });

            /* Fermeture */
        $('form').on('click', '.bloc-optionsAffichage-fermeture', function(){
           $(this).closest('div').removeClass('actif');
        });

        /* Supprimer */
        $('form').on('click', '.supprimerBloc', function(e){
            e.preventDefault();

            $(this).next('div').show();
        });

        $('form').on('click', '.suppressionBloc-annuler', function(e){
            e.preventDefault();

            $(this).closest('.suppressionBloc').hide().closest('.bloc-menu').removeClass('actif');
        });

        $('form').on('click', '.suppressionBloc-supprimer', function(e){
            e.preventDefault();

            $(this).closest('.field-bloc').slideUp(600, function(){
                $(this).remove();
                saveCloseFormulaire();
            });
        });

        $('form').on('click', '.suppressionBlocAnnexe-supprimer', function(e){
            e.preventDefault();

            $(this).closest('.field-bloc_annexe').slideUp(600, function(){
                $(this).remove();
                saveCloseFormulaire();
            });

            type = $(this).closest('.field-bloc_annexe').find('.type input').val();
            $('#'+type).removeClass('disabled');
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
                console.log(data);
                $('#export-bdd + .loader').hide();

                timestamp = data.substring(0, data.indexOf('*'));
                date = data.substring(data.indexOf('*')+1);

                $('#dumps-bdd tbody').append('<tr>\n' +
                    '                            <td>'+date+'</td>\n' +
                    '                            <td><a class="telechargementDump" href="/admin/sauvegarde/telechargerDump?type=bdd&fichier=dump'+timestamp+'.zip"><i class="fas fa-file-download"></i></a></td>\n' +
                    '                            <td><a id="dump'+timestamp+'.zip" href="#" class="export-bdd-supprimer"><i class="fas fa-trash"></i></a></td>\n' +
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
                    '                        <td><a class="telechargementDump" href="/admin/sauvegarde/telechargerDump?type=mediatheque&fichier=mediatheque'+timestamp+'.zip"><i class="fas fa-file-download"></i></a></td>\n' +
                    '                        <td><a id="mediatheque'+timestamp+'.zip" href="#" class="export-mediatheque-supprimer"><i class="fas fa-trash"></i></a></td>\n' +
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

    $('#page_active_langue').change(function(){
        langue = $('#page_active_langue').val();
        $('label[for^="page_active_traductions_"]').show().next('select').show();
        $('label[for="page_active_traductions_'+langue+'"]').hide().next('select').hide();
    });

    /* Une seule langue par défaut */
    $('body.list-langue td[data-label="Defaut"] input').click(function(){
       if($(this).is(':checked')){
           $('body.list-langue td[data-label="Defaut"] input').not(this).attr("checked", false);
       }
    });

    /* Activer le bouton d'enregistrement lors de la première modif d'un formulaire */
    $('form').on('change keyup', function(){
        saveCloseFormulaire();
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

    //Ajout de blocs via liste des blocs
    $('.listeBlocs li').click(function(){
        type = $(this).attr('id');
        $('.listeBlocs').addClass('chargement');
        entite = $('.listeBlocs').siblings('form').attr('name');

        $.ajax({
            url: Routing.generate('ajouterBloc'),
            method: "post",
            data: {type: type, typeBloc: 'Bloc'}
        })
            .done(function(data){
                saveCloseFormulaire();

                $('.listeBlocs').removeClass('actif chargement');
                count = $('#'+entite+'_blocs').find('.field-bloc').length;

                var form = data.replace(/bloc_/g, entite+'_blocs_'+count+'_')
                    .replace(/bloc\[/g, entite+'[blocs]['+count+'][');

                bloc = '<div id="nvBloc'+count+'" class="form-group field-bloc">'+form+'</div>';
                if($('.listeBlocs').attr('id') === 'apres'){
                    $('#'+entite+'_blocs').append(bloc);
                }else{
                    $('#'+entite+'_blocs').prepend(bloc);
                }

                $('#'+entite+'_blocs').prev('.empty').remove();

                $('.field-bloc').each(function(){
                    $(this).find("input[id$='position']").val($(this).index());
                });

                tinymce.remove();
                tinymce.init(optionsTinyMCE);

                location.href = "#";
                location.href = "#nvBloc"+count;
            })
            .fail(function(){
                $('.listeBlocs').removeClass('actif chargement');
            });
    });

    //Ajout de blocs annexes via liste des blocs
    $('.listeBlocsAnnexes li').click(function(){
        if(!$(this).hasClass('disabled')) {
            btnAjoutBloc = $(this);

            type = $(this).attr('id');
            $('.listeBlocsAnnexes').addClass('chargement');

            $.ajax({
                url: Routing.generate('ajouterBloc'),
                method: "post",
                data: {type: type, typeBloc: 'BlocAnnexe'}
            })
                .done(function (data) {
                    saveCloseFormulaire();

                    $('.listeBlocsAnnexes').removeClass('actif chargement');
                    count = $('#page_active_blocsAnnexes').find('.field-bloc_annexe').length;

                    var form = data.replace(/bloc_annexe_/g, 'page_active_blocsAnnexes_' + count + '_')
                        .replace(/bloc_annexe\[/g, 'page_active[blocsAnnexes][' + count + '][');

                    bloc = '<div id="nvBlocAnnexe' + count + '" class="form-group field-bloc_annexe">' + form + '</div>';
                    if ($('.listeBlocsAnnexes').attr('id') === 'apres') {
                        $('#page_active_blocsAnnexes').append(bloc);
                    } else {
                        $('#page_active_blocsAnnexes').prepend(bloc);
                    }

                    $('#page_active_blocsAnnexes').prev('.empty').remove();

                    $('.field-blocAnnexe').each(function () {
                        $(this).find("input[id$='position']").val($(this).index());
                    });

                    tinymce.remove();
                    tinymce.init(optionsTinyMCE)
                })
                .fail(function () {
                    $('.listeBlocsAnnexes').removeClass('actif chargement');
                });
        }
    });

        //Fermeture
    $('.listeBlocs-fermeture').click(function(){
       $('.listeBlocs').removeClass('actif');
    });

    $('.listeBlocsAnnexes-fermeture').click(function(){
       $('.listeBlocsAnnexes').removeClass('actif');
    });

    //Bloc formulaire : affichage ou non des choix
    $('#page_active_blocs').on('change', '.bloc-formulaire select[id$="type"]', function(){
       if($(this).val() === 'select' || $(this).val() === 'radio' || $(this).val() === 'checkbox'){
           $(this).closest('div').siblings('.field-choix').slideDown();
       }else{
           $(this).closest('div').siblings('.field-choix').slideUp().find('div[id$="choix"]').remove();
       }
    });

    //Toggle blocs et blocs annexes
    $('#page_active_blocs, #groupeblocs_blocs, #page_active_blocsAnnexes').on('click', '.toggleBloc', function(){
        $(this).closest('div[class^="form-group field-bloc"]').find('.contenu').children('div').toggleClass('hide');
        $(this).find('svg').toggleClass('fa-chevron-circle-down fa-chevron-circle-up');
        if($(this).find('svg').hasClass('fa-chevron-circle-up')){
            $('html, body').animate({
                scrollTop: $(this).closest('div[class^="form-group field-bloc"]').offset().top - 120
            }, 200);
        }else{
            $(this).closest('div[class^="form-group field-bloc"]').removeClass('focus');
        }
    });

    //Mise en avant du bloc en cours d'édition
    $('.field-bloc').click(function(){
        if(!$(this).hasClass('focus')){
            $('.field-bloc').removeClass('focus');
            $(this).addClass('focus');
        }
    });

    $('.field-bloc_annexe').click(function(){
        if(!$(this).hasClass('focus')){
            $('.field-bloc_annexe').removeClass('focus');
            $(this).addClass('focus');
        }
    });

    //Page de configuration des blocs
        //Activation/désactivation
    $('.configBlocs-bloc-actif').on('change', function(){
        checkbox = $(this);
        actif = $(this).is(':checked');
        type = $(this).closest('tr').attr('id');
        typeBloc = $(this).closest('table').attr('id');
        $.ajax({
            url: window.location.href,
            data: {action: 'actif', typeBloc: typeBloc, type: type, actif: actif}
        })
            .fail(function(){
                checkbox.attr('checked', !actif);
                checkbox.attr('disabled', true);
            })
    });

        //Changement de priorité
    $(".configBlocs tbody").sortable({
        handle: '.dragConfigBloc',
        update: function(event, ui){
            blocs = {};
            typeBloc = $(this).closest('table').attr('id');
            $(this).closest('table').find('tbody tr').each(function(){
                type = $(this).attr('id');
                priorite = $(this).index() + 1;
                blocs[type] = priorite;
            });
            $.ajax({
                url: window.location.href,
                data: {action: 'priorite', blocs: blocs, typeBloc: typeBloc}
            })
                .fail(function(){
                    $('.content-wrapper').prepend('<p>Une erreur est survenue</p>');
                })
        }
    });

    //Confirmation fermeture page après avoir cliqué sur "créer une traduction"
    function parseURLParams(url) {
        var queryStart = url.indexOf("?") + 1,
            queryEnd   = url.indexOf("#") + 1 || url.length + 1,
            query = url.slice(queryStart, queryEnd - 1),
            pairs = query.replace(/\+/g, " ").split("&"),
            parms = {}, i, n, v, nv;

        if (query === url || query === "") return;

        for (i = 0; i < pairs.length; i++) {
            nv = pairs[i].split("=", 2);
            n = decodeURIComponent(nv[0]);
            v = decodeURIComponent(nv[1]);

            if (!parms.hasOwnProperty(n)) parms[n] = [];
            parms[n].push(nv.length === 2 ? v : null);
        }
        return parms;
    }

    if($('body').hasClass('new')){
        get = parseURLParams(location.href);
        if(get.confirmation){
            saveCloseFormulaire();
        }
    }
});