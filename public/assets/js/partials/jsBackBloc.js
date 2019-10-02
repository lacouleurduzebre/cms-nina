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

                if(count === 0){
                    count = $('#'+entite+'_blocs').find('.field-bloc').length;
                }else{
                    count++;
                }

                var form = data.replace(/bloc_/g, entite+'_blocs_'+count+'_')
                    .replace(/bloc\[/g, entite+'[blocs]['+count+'][');

                bloc = '<div id="nvBloc'+count+'" class="form-group field-bloc nvBloc">'+form+'</div>';
                if($('.listeBlocs').attr('id') === 'apres'){
                    $('#'+entite+'_blocs').append(bloc);
                }else{
                    $('#'+entite+'_blocs').prepend(bloc);
                }

                //Anim ajout de bloc
                $('.field-bloc').removeClass('focus');
                nvBloc = $('#nvBloc'+count);
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

                $('#'+entite+'_blocs').prev('.empty').remove();

                $('.field-bloc').each(function(){
                    $(this).find("input[id$='position']").val($(this).index());
                });

                tinymce.remove();
                tinymce.init(optionsTinyMCE);
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
    });

    $('.listeBlocsAnnexes-fermeture').click(function(){
        $('.listeBlocsAnnexes').removeClass('actif');
    });

    //Bloc groupe de blocs : modif du lien si valeur qui change
    $('.bloc-groupeblocs-edition select').on('change', function(){
        $(this).next('a').attr('href', Routing.generate('admin', { action: 'edit', entity: 'GroupeBlocs', id: $(this).val() }));
    });

    //Toggle blocs et blocs annexes
    $('#page_active_blocs, #groupeblocs_blocs, #page_active_blocsAnnexes').on('click', '.toggleBloc', function(){
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
            console.log(rotate);
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
    });

    $('.listeBlocs, .listeBlocsAnnexes').click(function(){
        $(this).removeClass('actif');
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
});