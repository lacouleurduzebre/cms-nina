$(document).ready(function(){
    clicEnregistrement = false;
    count = 0;
    countBA = 0;

    //Bouton d'enregistrement / Confirmation fermeture page
    saveCloseFormulaire = function(){
        $('.formulaire-actions-enregistrer').attr("disabled", false);
        $(window).bind('beforeunload', function(){
            if(!clicEnregistrement){
                return 'Êtes-vous sûr de vouloir quitter cette page ? Des données pourraient ne pas avoir été enregistrées';
            }
        });
    };

    /* Initialisation TinyMCE */
    tinymce.init(optionsTinyMCEParagraphe);
    tinymce.init(optionsTinyMCE);

    /* Pop-up pour confirmer une suppression */
    $('.action-delete').click(function(e){
        e.preventDefault();
        $('#modal-delete').css('display', 'flex');
    });

    /* Résolution du problème de textarea vide avec tinymce + enregistrement onglet actif */
    $('.formulaire-actions-enregistrer').click(function(){
        if($('.nav-tabs li.active').length > 0){
            Cookies.set('ongletActif', $('.nav-tabs li.active a').attr('id'), { expires: 7 });
        }
        clicEnregistrement = true;
        tinyMCE.triggerSave();
    });

    $('.action-save').click(function(){
        tinyMCE.triggerSave();
    });

    /* Restauration de l'onglet actif */
    if(Cookies.get('ongletActif')){
        $('#'+Cookies.get('ongletActif')).click();
        Cookies.remove('ongletActif');
    }

    /* URL automatique */
    str2url = function(str,encoding,ucfirst)
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
    };

    creationURL = function( event ){
        if($('body').hasClass('new')) {
            titre = $(this).val();
            url = str2url(titre, 'UTF-8', true);
            $(event.data.cible).val(url);
            apercuGoogle($(event.data.cible));
        }
    };

        /* Pour les pages */
        $('#page_active_titre').on('keyup', {
            cible: '#page_active_SEO_url'
        }, creationURL );

        /* Pour les catégories */
        $('#categorie_nom').on('keyup', {
            cible: '#categorie_SEO_url'
        }, creationURL );

        /* Pour les types de catégorie */
        $('#typecategorie_nom').on('keyup', {
            cible: '#typecategorie_SEO_url'
        }, creationURL );

        /* Modification de l'url */
        urlPropre = function(){
            urlNonFormattee = $(this).val();
            url = str2url(urlNonFormattee, 'UTF-8', true);
            $(this).val(url);
        };

        $('input[id$="SEO_url"]').on('keyup', urlPropre);

            /* Ajout de page enfant dans l'arbo */
        $('body').on('keyup', '#ajoutPage-url', urlPropre);

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
        }else if(id === 'configuration_logo' || id === 'configuration_favicon'){//Logo et favicon du site
            $('#'+id).siblings('.apercuLogo').find('img').attr('src', urlImg);
        }else{//Bloc Image ou Vidéo
            $('#'+id).parent('div').next('div').find('img, iframe').attr('src', urlImg).show();
        }

        saveCloseFormulaire();
    });

    /* Toggle pour les éléments de tableau en dessous de 768px */
    $('.toggleElementTableau').click(function(){
       $(this).closest('tr').toggleClass('ouvert');
    });

    /* Activer le bouton d'enregistrement lors de la première modif d'un formulaire */
    $('form').on('change keyup', function(){
        if(!$(this).hasClass('popupDesactive')){
            saveCloseFormulaire();
        }
    });

    $('form').submit(function(){
        clicEnregistrement = true;
    });

    /* Fermeture des messages flash */
    $('#flash-messages').on('click', 'svg', function(){
       $(this).closest('div').fadeOut();
    });

    //Score SEO
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

    //Onglet référencement des pages, catégories et types de catégorie
        //Score
    if($('body').hasClass('new') || $('body').hasClass('edit')){
        $('input[id$="SEO_url"]').each(function(){
            scoreSEOChargement($(this), 75);
        });
        $('input[id$="SEO_metaTitre"]').each(function(){
            scoreSEOChargement($(this), 65);
        });
        $('textarea[id$="SEO_metaDescription"]').each(function(){
            scoreSEOChargement($(this), 150);
        });
    }

    $('input[id$="SEO_url"]').on('keyup', function(){
        scoreSEOChargement($(this), 75);
    });
    $('input[id$="SEO_metaTitre"]').on('keyup', function(){
        scoreSEOChargement($(this), 65);
    });
    $('textarea[id$="SEO_metaDescription"]').on('keyup', function(){
        scoreSEOChargement($(this), 150);
    });

    $('input[id$="SEO_url"], input[id$="SEO_metaTitre"], textarea[id$="SEO_metaDescription"]').on('keyup', function(){
        $('.raz').prop('disabled', false);
        apercuGoogle($(this));
    });

        //Aperçu Google
    apercuGoogle = function(elem){
        identifiant = elem.attr('id').split('_').pop();
        seo = elem.val();

        if(identifiant === 'metaTitre'){
            if(seo.length > 65){
                seo = seo.substr(0, 65)+'...';
            }
        }else if(identifiant === 'url'){
            if(seo.length > 75){
                seo = seo.substr(0, 75)+'...';
            }
            seo = $('.listeSEO-SEO .'+identifiant).find('span')[0].outerHTML + seo;
        }else{
            if(seo.length > 150){
                seo = seo.substr(0, 150)+'...';
            }
        }

        $('.listeSEO-SEO .' + identifiant).html(seo);
    };

    razApercuGoogle = function(){
        $('input[id$="SEO_url"], input[id$="SEO_metaTitre"], textarea[id$="SEO_metaDescription"]').each(function(){
            apercuGoogle($(this));
        });
    };

        //Raz
    getChampTitre = function(elem){
        entite = elem.closest('form').data('entity').toLowerCase();
        if(entite === 'page_active'){
            titre = $('#'+entite+'_titre').val();
        }else{
            titre = $('#'+entite+'_nom').val();
        }
        return titre;
    };

    // $('#onglet_SEO > .raz').click(function(){
    //     if(!$(this).hasClass('ok')){
    //         titre = getChampTitre($(this));
    //
    //         $('input[id$="SEO_url"]').each(function(){
    //             $(this).val(str2url(titre.substr(0, 75)));
    //             scoreSEOChargement($(this), 75);
    //         });
    //         $('input[id$="SEO_metaTitre"]').each(function(){
    //             $(this).val(titre.substr(0, 65));
    //             scoreSEOChargement($(this), 65);
    //         });
    //         $('textarea[id$="SEO_metaDescription"]').each(function(){
    //             $(this).val(titre.substr(0, 150));
    //             scoreSEOChargement($(this), 150);
    //         });
    //
    //         saveCloseFormulaire();
    //
    //         $(this).addClass('ok');
    //
    //         razApercuGoogle();
    //     }
    // });

    $('#onglet_SEO > .raz').click(function(e){
            e.preventDefault();

            tinyMCE.triggerSave();

            titre = getChampTitre($(this));

            entite = $(this).closest('form').attr('name');

            $('input[id$="SEO_url"]').each(function(){
                $(this).val(str2url(titre.substr(0, 75)));
                scoreSEOChargement($(this), 75);
            });
            $('input[id$="SEO_metaTitre"]').each(function(){
                $(this).val(titre.substr(0, 65));
                scoreSEOChargement($(this), 65);
            });
            $('textarea[id$="SEO_metaDescription"]').each(function(){
                description = titre;

                if(entite === 'page_active'){
                    bloc = $('#page_active_blocs .bloc-paragraphe').find('textarea');
                    if(bloc.length <= 0){
                        bloc = $('#page_active_blocs .bloc-texte').find('textarea');
                    }

                    if(bloc.length > 0){
                        description = $('<textarea />').html(bloc.val().replace(/<\/?[^>]+>/gi, '')).text();
                    }
                }else{
                    if($('textarea[id$="description"]').val().length > 0){
                        description = $('<textarea />').html($('textarea[id$="description"]').val().replace(/<\/?[^>]+>/gi, '')).text();
                    }
                }

                $(this).val(description.substr(0, 150));
                scoreSEOChargement($(this), 150);
            });

            saveCloseFormulaire();

            razApercuGoogle();

            $(this).prop('disabled', true);

            messageFlash('enregistrement', "Champs SEO réinitialisés");
    });

        //Si méta-titre, url ou méta-description vide en perdant le focus, on reprend le titre
    $('input[id$="SEO_url"]').focusout(function(){
        titre = getChampTitre($(this));
        if($(this).val() === ''){
            $(this).val(str2url(titre));
            scoreSEOChargement($(this), 75);
            apercuGoogle($(this));
        }
    });

    $('input[id$="SEO_metaTitre"]').focusout(function(){
        titre = getChampTitre($(this));
        if($(this).val() === ''){
            $(this).val(titre);
            scoreSEOChargement($(this), 65);
            apercuGoogle($(this));
        }
    });

    $('textarea[id$="SEO_metaDescription"]').focusout(function(){
        titre = getChampTitre($(this));
        if($(this).val() === ''){
            $(this).val(titre);
            scoreSEOChargement($(this), 150);
            apercuGoogle($(this));
        }
    });

    //Cacher le message "enregistrement terminé"
    setTimeout(function(){
        $('.alert').fadeOut();
    }, 3000);

    //Message flash
    messageFlash = function(type, message){
        $('#flash-messages').append("<div class='alert alert-"+type+"'><span>"+message+"</span><i class='fas fa-times'></i></div>");

        setTimeout(function(){
            $('.alert').fadeOut();
        }, 3000);
    };

    //Onglet actif en fonction de l'url
    parseURLParams = function(url){
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
    };

    get = parseURLParams(location.href);
    if(typeof(get) != 'undefined'){
        if(get.activeTab){
            $('.nav-tabs li:nth-of-type('+get.activeTab[0]+') > a').click();
        }
    }

    //Vider le cacher
    $('#viderCache').click(function(e){
        e.preventDefault();
        $('.conteneurChargement').addClass('actif');
        $.ajax("/admin/traductions/templates").done(function(){
            $('.conteneurChargement').removeClass('actif');
            alert('Le cache a été vidé');
        });
    });

    //Modal
    $('[data-modal]').click(function(){
        idModal = $(this).attr('data-modal');
        $('#'+idModal).fadeIn('slow', function(){
            $('#'+idModal).css({
                'display' : 'flex',
                'opacity' : 1
            });
        });
    });

    $('.modal-box, .modal-close').click(function(){
        $(this).closest('.modal-box').fadeOut('slow', function(){
            $('#'+idModal).css('opacity', 0);
        });
    });

    $('.modal-content').click(function(e){
        e.stopPropagation();
    });

    //Enregistrement des entités via ajax
    $(".edit-form, .new-form").submit(function(e) {

        e.preventDefault();

        bouton = $('.formulaire-actions-enregistrer');
        texte = bouton.html();
        largeur = bouton.width();

        bouton.attr('disabled', true).width(largeur).html('<i class="fas fa-sync fa-spin"></i>');

        var form = $(this);

        $('.error-block, .nav-tabs .label-danger').remove();

        $.ajax({
            type: "POST",
            url: window.location.href,
            data: form.serialize(),
            success: function(data)
            {
                if(data.erreurs === true){
                    form.unbind('submit').submit();
                }else{
                    bouton.width('auto').html(texte);

                    $('#flash-messages').append(data.tpl);

                    setTimeout(function(){
                        $('.alert').fadeOut();
                    }, 3000);
                }
            }
        });
    });

    $('.select-multiple').select2();
});