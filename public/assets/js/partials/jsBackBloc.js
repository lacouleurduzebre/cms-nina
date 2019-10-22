$(document).ready(function() {
    /* Gestion de la position des blocs */
    options = {
        handle: '.drag',
        update: function(event, ui){
            $('.field-bloc').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
                saveCloseFormulaire();
            });
            tinymce.remove();
            tinymce.init(optionsTinyMCEParagraphe);
            tinymce.init(optionsTinyMCE);
        }
    };

    $("#page_active_blocs").sortable(options);
    $("#groupeblocs_blocs").sortable(options);
    $("div[id$='blocsEnfants']").sortable(options);

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

    $(".bloc-grille div[id$='contenu_cases']").sortable({
        handle: '.dragCase',
        update: function(event, ui){
            $('.field-case').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
                saveCloseFormulaire();
            });
        }
    });

    $(".bloc-accordeon div[id$='contenu_sections']").sortable({
        handle: '.dragCase',
        update: function(event, ui){
            $('.field-section').each(function(){
                $(this).find("input[id$='position']").val($(this).index());
                saveCloseFormulaire();
            });
        }
    });

    /* Menu blocs */
    $('form').on('click', '.bloc-menu', function(e){
        e.stopPropagation();
        $('.bloc-menu').not(this).closest('.bloc-barreActions').removeClass('actif');
        $(this).closest('.bloc-barreActions').toggleClass('actif');
        $(this).closest('div').find('.suppressionBloc').hide();
    });

    $('body').on('click', function(){
        $('.bloc-barreActions').removeClass('actif');
        $('.suppressionBloc').hide();
    });

    //Sauvegarde - Fermeture
    $('form').on('click', '.bloc-panel--sauvegarde', function(){
        formulaire = $(this).closest('.bloc-panel');
        //Fermeture du formulaire
        formulaire.addClass('hidden');

        //Enregistrement des valeurs du formulaire
        formulaire.find('input').each(function(){
            if($(this).attr('type') !== 'checkbox' && $(this).attr('type') !== 'radio'){//Text, color, mail,...
                $(this).attr('value', $(this).val());
            }else{//Radio ou checkbox
                console.log($(this).prop('checked'));
                $(this).attr('checked', $(this).prop('checked'));
            }
        });

        tinyMCE.triggerSave();
        formulaire.find('textarea').each(function(){
            $(this).html($(this).val());
        });

        formulaire.find('select option').each(function(){
            $(this).attr('selected', $(this).prop('selected'));
        });

        //Suppression du prototype
        $(this).closest('.field-bloc').children('.bloc-barreActions').children('.prototype').remove();
    });

    //Mise à jour de l'aperçu du bloc
    $('form').on('click', '.bloc-formulaire--sauvegarde', function(){
        conteneurApercu = $(this).closest('.bloc-formulaire').prev('.bloc-apercu');
        idBloc = $(this).closest('.contenu').data('bloc');
        typeBloc = $(this).closest('.bloc-formulaire').data('type');
        contenu = '';
        tinyMCE.triggerSave();
        $(this).closest('.bloc-panel').find('input').each(function(){
            if(($(this).attr('type') !== 'checkbox' || $(this).attr('type') !== 'radio') || $(this).attr('checked')){
                contenu = (contenu === '') ? contenu+$(this).serialize() : contenu+'&'+$(this).serialize();
            }
        });
        $(this).closest('.bloc-panel').find('textarea').each(function(){
            contenu = (contenu === '') ? contenu+$(this).serialize() : contenu+'&'+$(this).serialize();
        });
        $(this).closest('.bloc-panel').find('select').each(function(){
            contenu = (contenu === '') ? contenu+$(this).serialize() : contenu+'&'+$(this).serialize();
        });
        $.ajax({
            url: '/admin/bloc/apercuBloc',
            method: "post",
            data: {contenu: contenu, idBloc: idBloc, typeBloc: typeBloc}
        })
            .done(function(data){
                conteneurApercu.html(data);
            })
            .fail(function(){
            });
    });

    //Annulation -> restauration des valeurs
    $('form').on('click', '.bloc-panel--annulation', function(){
        formulairePrecedent = $(this).closest('.field-bloc').children('.bloc-barreActions').children('.prototype');

        $(this).closest('.bloc-panel').addClass('hidden').html(formulairePrecedent.html());

        formulairePrecedent.remove();

        tinymce.remove();
        tinymce.init(optionsTinyMCEParagraphe);
        tinymce.init(optionsTinyMCE);
    });

    /* Monter */
    $('form').on('click', '.monterBloc', function(e){
        e.preventDefault();
        $(this).closest('div').removeClass('actif');

        bloc = $(this).closest('.field-bloc');
        blocPrecedent = bloc.prev('.field-bloc');
        bloc.insertBefore(blocPrecedent);

        tinymce.remove();
        tinymce.init(optionsTinyMCEParagraphe);
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
        tinymce.init(optionsTinyMCEParagraphe);
        tinymce.init(optionsTinyMCE);

        $('.field-bloc').each(function(){
            $(this).find("input[id$='position']").val($(this).index());
        });
    });

    //Formulaire temporaire
    formulaireTemporaire = function(bouton, classFormulaire){
        bloc = bouton.closest('.field-bloc');

        formulaire = bloc.children('div').children('.'+classFormulaire);

        formulaire.removeClass('hidden');

        bloc.children('.bloc-barreActions').append('<div class="prototype hidden">'+formulaire.html()+'</div>');
    };

    /* Formulaire */
    $('form').on('click', '.bloc-edit', function(e){
        e.preventDefault();

        formulaireTemporaire($(this), 'bloc-formulaire');
    });

    /* Options d'affichage */
    $('form').on('click', '.optionsAffichage', function(e){
        e.preventDefault();

        formulaireTemporaire($(this), 'bloc-optionsAffichage');
    });

    /* Supprimer */
    $('form').on('click', '.supprimerBloc', function(e){
        e.preventDefault();
        e.stopPropagation();
        $(this).next('div').show();
    });

    $('form').on('click', '.suppressionBloc-annuler', function(e){
        e.preventDefault();

        $(this).closest('.suppressionBloc').hide().closest('.bloc-menu').removeClass('actif');
    });

    $('form').on('click', '.suppressionBloc-supprimer', function(e){
        e.preventDefault();

        if(count === 0){
            entite = $('.listeBlocs').siblings('form').attr('name');
            count = $('#'+entite+'_blocs').find('.field-bloc').length;
        }

        $(this).closest('.field-bloc').fadeTo(500, 0, function(){
            $(this).slideUp(300, function(){
                $(this).remove();
            });
            saveCloseFormulaire();
        });

        //Bloc enfant
        if($(this).closest('.blocsEnfants').length > 0){
            input = $(this).closest('.contenu').find('input[name$="[colonnes]"]:checked');
            verifNombreBlocs(input, 'suppression');
        }
    });

    $('form').on('click', '.suppressionBlocAnnexe-supprimer', function(e){
        e.preventDefault();

        if(count === 0){
            count = $('#page_active_blocsAnnexes').find('.field-bloc_annexe').length;
        }

        $(this).closest('.field-bloc_annexe').fadeTo(500, 0, function(){
            $(this).slideUp(300, function(){
                $(this).remove();
            });
            saveCloseFormulaire();
        });

        type = $(this).closest('.field-bloc_annexe').find('.type input').val();
        $('#'+type).removeClass('disabled');
    });

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

                if($('.listeBlocs').attr('id') === 'avant' || $('.listeBlocs').attr('id') === 'apres'){
                    if(count === 0){
                        count = $('#'+entite+'_blocs').find('.field-bloc').length;
                    }else{
                        count++;
                    }

                    var form = data.replace(/bloc_/g, entite+'_blocs_'+count+'_')
                        .replace(/bloc\[/g, entite+'[blocs]['+count+'][');

                    bloc = '<div id="nvBloc'+count+'" class="form-group field-bloc nvBloc w100 bloc-'+type.toLowerCase()+'" data-name="'+count+'">'+form+'</div>';
                    if($('.listeBlocs').attr('id') === 'apres'){
                        $('#'+entite+'_blocs').append(bloc);
                    }else{
                        $('#'+entite+'_blocs').prepend(bloc);
                    }

                    nvBloc = $('#nvBloc' + count);
                }else{//Bloc enfant
                    section = $('#'+$('.listeBlocs').attr('data-section'));

                    count = section.closest('.field-bloc').data('name');
                    countBloc = section.find('.field-bloc').length;

                    exp = entite+'['+$('.listeBlocs').attr('data-section').replace(entite+'_', '').replace(/_/g, '][')+']';

                    var form = data.replace(/bloc_/g, $('.listeBlocs').attr('data-section')+'_'+countBloc+'_')
                        .replace(/bloc\[/g, exp+'['+countBloc+'][');

                    bloc = '<div id="nvBloc'+countBloc+'" class="form-group field-bloc w100 bloc-'+type.toLowerCase()+'" data-name="'+countBloc+'">'+form+'</div>';

                    if($('.listeBlocs').attr('data-position') === 'avant'){
                        section.prepend(bloc);
                    }else{
                        section.append(bloc);
                    }

                    nvBloc = $('#nvBloc' + countBloc);
                }

                //Anim ajout de bloc
                var elOffset = nvBloc.offset().top;
                var elHeight = nvBloc.height();
                var windowHeight = $(window).height();

                if (elHeight < windowHeight) {
                    offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
                }
                else {
                    offset = elOffset;
                }

                nvBloc.find('.bloc-panel.bloc-formulaire').removeClass('hidden');

                $('body, html').animate({
                    scrollTop: offset
                }, 600, 'swing', function(){
                    nvBloc.fadeTo(600, 1);
                });

                $('#'+entite+'_blocs').prev('.empty').remove();

                //Màj de la position
                $('.field-bloc').each(function(){
                    $(this).find("input[id$='position']").val($(this).index());
                });

                tinymce.remove();
                tinymce.init(optionsTinyMCEParagraphe);
                tinymce.init(optionsTinyMCE);
                $('.select-multiple').select2();
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

                    if(count === 0){
                        count = $('#page_active_blocsAnnexes').find('.field-bloc_annexe').length;
                    }else{
                        count++;
                    }

                    var form = data.replace(/bloc_annexe_/g, 'page_active_blocsAnnexes_' + count + '_')
                        .replace(/bloc_annexe\[/g, 'page_active[blocsAnnexes][' + count + '][');

                    bloc = '<div id="nvBlocAnnexe' + count + '" class="form-group field-bloc_annexe nvBloc">' + form + '</div>';
                    if ($('.listeBlocsAnnexes').attr('id') === 'apres') {
                        $('#page_active_blocsAnnexes').append(bloc);
                    } else {
                        $('#page_active_blocsAnnexes').prepend(bloc);
                    }

                    //Anim ajout de bloc
                    $('.field-bloc_annexe').removeClass('focus');
                    nvBloc = $('#nvBlocAnnexe'+count);
                    nvBloc.addClass('focus');

                    var elOffset = nvBloc.offset().top;
                    var elHeight = nvBloc.height();
                    var windowHeight = $(window).height();

                    if (elHeight < windowHeight) {
                        offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
                    }
                    else {
                        offset = elOffset;
                    }

                    $('body, html').animate({
                        scrollTop: offset
                    }, 600, 'swing', function(){
                        nvBloc.fadeTo(600, 1);
                    });
                    //

                    $('#page_active_blocsAnnexes').prev('.empty').remove();

                    $('.field-blocAnnexe').each(function () {
                        $(this).find("input[id$='position']").val($(this).index());
                    });

                    tinymce.remove();
                    tinymce.init(optionsTinyMCEParagraphe);
                    tinymce.init(optionsTinyMCE);

                    $('#'+type).addClass('disabled');
                })
                .fail(function () {
                    $('.listeBlocsAnnexes').removeClass('actif chargement');
                });
        }
    });

    //Fermeture
    $('.listeBlocs-fermeture').click(function(){
        $('.listeBlocs').removeClass('actif');
        $('.voirBlocs').removeClass('hidden');
    });

    $('.listeBlocsAnnexes-fermeture').click(function(){
        $('.listeBlocsAnnexes').removeClass('actif');
        $('.voirBlocs').removeClass('hidden');
    });

    //Bloc groupe de blocs : modif du lien si valeur qui change
    $('.bloc-groupeblocs-edition select').on('change', function(){
        $(this).next('a').attr('href', Routing.generate('admin', { action: 'edit', entity: 'GroupeBlocs', id: $(this).val() }));
    });

    /*//Toggle blocs - pages
    $('#page_active_blocs').on('click', '.toggleBloc', function(){
        $(this).closest('.form-group').find('.contenu').children('div').toggleClass('hide');
        $(this).toggleClass('rotate');

        if($('#page_active_blocs').find('.toggleBloc:not(.rotate)').length === 0){//Si tous les blocs sont fermés, "déplier les blocs"
            $('#deplierBlocs').show();
            $('#replierBlocs').hide();
        }else if($('#page_active_blocs').find('.toggleBloc.rotate').length === 0){//Si tous les blocs sont ouverts, "replier les blocs"
            $('#replierBlocs').show();
            $('#deplierBlocs').hide();
        }
    });

    //Toggle blocs - groupes de blocs
    $('#groupeblocs_blocs').on('click', '.toggleBloc', function(){
        $(this).closest('.form-group').find('.contenu').children('div').toggleClass('hide');
        $(this).toggleClass('rotate');

        if($('#groupeblocs_blocs').find('.toggleBloc:not(.rotate)').length === 0){//Si tous les blocs sont fermés, "déplier les blocs"
            $('#deplierBlocs').show();
            $('#replierBlocs').hide();
        }else if($('#groupeblocs_blocs').find('.toggleBloc.rotate').length === 0){//Si tous les blocs sont ouverts, "replier les blocs"
            $('#replierBlocs').show();
            $('#deplierBlocs').hide();
        }
    });*/

    //Mise en avant du bloc en cours d'édition
    $('#page_active_blocs, #groupeblocs_blocs, #page_active_blocsAnnexes').on('click', '.field-bloc', function(){
        if(!$(this).hasClass('focus')){
            $('.field-bloc').removeClass('focus');
            $(this).addClass('focus');
        }
    });

    $('#page_active_blocs, #groupeblocs_blocs, #page_active_blocsAnnexes').on('click', '.field-bloc_annexe', function(){
        if(!$(this).hasClass('focus')){
            $('.field-bloc_annexe').removeClass('focus');
            $(this).addClass('focus');
        }
    });

    //Bloc formulaire : affichage ou non des choix
    $('#page_active_blocs').on('change', '.bloc-formulaire select[id$="type"]', function(){
        if($(this).val() === 'select' || $(this).val() === 'radio' || $(this).val() === 'checkbox'){
            $(this).closest('div').siblings('.field-choix').slideDown();
        }else{
            $(this).closest('div').siblings('.field-choix').slideUp().find('div[id$="choix"]').remove();
        }
    });

    //Déplier / replier tous les blocs
    toggleBlocs = function(elem, action){
        actionInverse = (action === 'r') ? 'd' : 'r';
        elem.closest('.form-group').find('.contenu').each(function(){
            if(action === 'r'){
                rotate = !$(this).children('div').hasClass('hide');
                $(this).children('div').addClass('hide');
            }else{
                rotate = $(this).children('div').hasClass('hide');
                $(this).children('div').removeClass('hide');
            }
            if(rotate){
                $(this).closest('.field-bloc').find('.toggleBloc').toggleClass('rotate');
            }
        });
        elem.hide();
        $('#'+actionInverse+'eplierBlocs').show();
    };

    $('#replierBlocs').click(function(e){
        e.preventDefault();
        toggleBlocs($(this), 'r');
    });

    $('#deplierBlocs').click(function(e){
        e.preventDefault();
        toggleBlocs($(this), 'd');
    });

    //Activation / désactivation des blocs
    $('#page_active_blocs, #groupeblocs_blocs').on('change', 'input[id$="_active"]', function(){
        if($(this).prop('checked')){
            $(this).closest('.field-bloc').removeClass('desactive');
        }else{
            $(this).closest('.field-bloc').addClass('desactive');
        }
    });

    //Bloc actif
    get = parseURLParams(location.href);
    if(get.blocActif){
        $('#page_active_blocs .contenu').not('[data-bloc="'+get.blocActif[0]+'"]').addClass('hide');

        bloc = $('[data-bloc="'+get.blocActif[0]+'"]');

        bloc.closest('.field-bloc').addClass('focus');

        var elOffset = bloc.offset().top;
        var elHeight = bloc.height();
        var windowHeight = $(window).height();

        if (elHeight < windowHeight) {
            offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
        }
        else {
            offset = elOffset;
        }

        $('body, html').animate({
            scrollTop: offset - 100
        }, 600, 'swing');
    }

    //Clic sur fond de la liste des blocs = fermeture
    $('.listeBlocs li, .listeBlocsAnnexes li').click(function(e){
        e.stopPropagation();
        $(this).siblings('li.blocCache').addClass('hidden');
        $('.voirBlocs').removeClass('hidden');
    });

    $('.listeBlocs, .listeBlocsAnnexes').click(function(){
        $(this).removeClass('actif');
        $(this).find('li.blocCache').addClass('hidden');
        $('.voirBlocs').removeClass('hidden');
    });

    //Liens
    $('body').on('change', 'input[name$="[typeLien]"]', function(){
        conteneur = $(this).closest('.lien-page');

        if($(this).val() === 'page' || $(this).val() === 'autre') {
            conteneur.find('.lien-page--blank').removeClass('hidden');
            if($(this).val() === 'page'){
                conteneur.find('.page').removeClass('hidden');
                conteneur.find('.autre').addClass('hidden').find('input').val('');
            }else{
                conteneur.find('.page').addClass('hidden').find('select').val('');
                conteneur.find('.autre').removeClass('hidden');
            }
        }else{
            conteneur.find('.page').addClass('hidden').find('select').val('');
            conteneur.find('.autre').addClass('hidden').find('input').val('');
            conteneur.find('.lien-page--blank').addClass('hidden');
        }

        conteneur.find('.lien-page--blank').find('input').prop('checked', false);
    });

    $('input[name$="[typeLien]"]').each(function(){
        conteneur = $(this).closest('.lien-page');

        if($(this).prop('checked') && $(this).val() === 'page'){
            conteneur.find('.page').removeClass('hidden');
            conteneur.find('.lien-page--blank').removeClass('hidden');
        }else if($(this).prop('checked') && $(this).val() === 'autre'){
            conteneur.find('.autre').removeClass('hidden');
            conteneur.find('.lien-page--blank').removeClass('hidden');
        }
    });

    //Bloc grille : type d'infos
    $('body').on('change', '.case-choix input', function(){
        conteneur = $(this).closest('.field-case');

        if($(this).val() === 'page'){
            conteneur.find('.case-page').removeClass('hidden');
            conteneur.find('.case-autre').addClass('hidden');
        }else{
            conteneur.find('.case-page').addClass('hidden');
            conteneur.find('.case-autre').removeClass('hidden');
        }
    });

    $('.case-choix input').each(function(){
        conteneur = $(this).closest('.field-case');

        if($(this).prop('checked') && $(this).val() === 'page'){
            conteneur.find('.case-page').removeClass('hidden');
        }else if($(this).prop('checked') && $(this).val() === 'autre'){
            conteneur.find('.case-autre').removeClass('hidden');
        }
    });

    //Aperçu du bloc vidéo
    $('body').on('keyup', '.bloc-video input[name$="[video]"]', function() {
        urlVideo = $(this).val();
        if (urlVideo.includes('youtube') || urlVideo.includes('youtu.be')) {
            if (urlVideo.includes('?v=')) {
                urlVideo = urlVideo.split('?v=').pop();
            } else {
                urlVideo = urlVideo.split('/').pop();
            }
            urlVideo = "https://www.youtube.com/embed/" + urlVideo + "?rel=0";
        }
        $(this).closest('.contenu').find('iframe').show().attr('src', urlVideo);
    });

    //Voir tous les blocs
    $('.voirBlocs').click(function(e){
        e.stopPropagation();
        $(this).addClass('hidden');
        $(this).prev('ul').find('.blocCache').removeClass('hidden');
    });

    //Changement des options d'affichage
        //Largeur
    $('body').on('change', 'select[name$="[largeur]"]', function() {
        $(this).closest('.field-bloc').removeClass('w100 w80 w75 w60 w50 w40 w25 w20').addClass('w'+$(this).val());
    });

        //Alignement horizontal
    $('body').on('change', 'select[name$="[alignementHorizontal]"]', function() {
        $(this).closest('.field-bloc').removeClass('mrauto mlauto').addClass($(this).val());
    });

        //Alignement vertical
    $('body').on('change', 'select[name$="[alignementVertical]"]', function() {
        $(this).closest('.field-bloc').removeClass('mtauto mbauto').addClass($(this).val());
    });

        //Alignement horizontal des enfants
    $('body').on('change', 'select[name$="[alignementHorizontalEnfants]"]', function() {
        $(this).closest('.field-bloc').children('div').children('.contenu').children('.blocsEnfants').children('div').css('justify-content', $(this).val());
    });

        //Alignement vertical des enfants
    $('body').on('change', 'select[name$="[alignementVerticalEnfants]"]', function() {
        $(this).closest('.field-bloc').children('div').children('.contenu').children('.blocsEnfants').children('div').css('align-items', $(this).val());
    });

        //Pleine largeur
    $('body').on('change', 'input[name$="[pleineLargeur]"]', function() {
        if($(this).prop('checked')){
            $(this).closest('.field-bloc').addClass('pleineLargeur');
        }else{
            $(this).closest('.field-bloc').removeClass('pleineLargeur');
        }
    });

        //Padding
            //Marges identiques <-> Marges différentes
    $('body').on('click', '.togglePadding', function() {
        blocPadding = $(this).closest('div').next('.bloc-padding');
        input = $(this).closest('div').siblings('input');
        selectTout = $(this).prev('select');

        if(blocPadding.hasClass('hidden')){//Marges identiques -> Marges différentes
            nouvelleVal = '';
            blocPadding.find('select').each(function(){
                nouvelleVal += $(this).val()+' ';
            });
            input.val(nouvelleVal);
            selectTout.attr('disabled', true);
        }else{//Marges différentes -> Marges identiques
            input.val(selectTout.val());
            selectTout.attr('disabled', false);
        }

        $(this).find('svg').toggleClass('fa-link fa-unlink');
        blocPadding.toggleClass('hidden');
    });

            //Changement marges identiques
    $('body').on('change', 'select[name$="[paddingTout]"]', function() {
        input = $(this).closest('div').siblings('input');
        input.val($(this).val());
    });

            //Changement marges différentes
    $('body').on('change', 'select[name$="[paddingGauche]"], select[name$="[paddingDroit]"], select[name$="[paddingHaut]"], select[name$="[paddingBas]"]', function() {
        input = $(this).closest('.bloc-padding').siblings('input');
        nouvelleVal = '';
        $(this).closest('.bloc-padding').find('select').each(function(){
            nouvelleVal += $(this).val()+' ';
        });
        input.val(nouvelleVal);
    });

    //Bloc réseaux sociaux : type d'utilisation (liens / partage)
    $('body').on('change', 'input[name$="[typeRS]"]', function(){
        conteneur = $(this).closest('.contenu');

        if($(this).val() === 'partage') {
            conteneur.find('.rs-input-partage').removeClass('hidden');
            conteneur.find('.rs-input-liens').addClass('hidden');
        }else{
            conteneur.find('.rs-input-partage').removeClass('hidden');
            conteneur.find('.rs-input-liens').removeClass('hidden');
        }
    });

    //Toggle mise en page et conteneurs
    $('#toggleConteneurs').click(function(){
        $(this).closest('div').toggleClass('conteneurs');
    });

    $('#toggleMiseEnPage').click(function(){
        $(this).closest('div').toggleClass('miseEnPage');
    });
});