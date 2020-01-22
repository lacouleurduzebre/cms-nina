$(document).ready(function(){
    //Envoi des formulaires
    $('.blocFormulaire-formulaire').on('submit', function(e){
        e.preventDefault();

        idBloc = $(this).attr('id');
        donnees = $(this).serializeArray();

        erreur = false;
        $('.erreur-bloc'+idBloc).remove();

        $(this).find('label.required').each(function(){
            idChamp = $(this).attr('for');
            if($('#'+idChamp).val() === ''){
                erreur = true;
                message = '<p class="erreur-bloc'+idBloc+'">Merci de compléter le champ "'+$(this).html().trim()+'"</p>';
                $(this).closest('div').append(message);
            }
        });

        if(erreur === false){
            $('.message'+idBloc).html('').removeClass('erreur');
            $.ajax({
                url: window.location.origin+"/envoiMail",
                method: "post",
                data: {donnees: donnees, idBloc: idBloc}
            })
                .done(function(data){
                    $('.message'+idBloc).prev('form').animate({opacity: 0}, function(){
                        $(this).css('visibility', 'hidden');
                    });
                    $('.message'+idBloc).html(data);
                });
        }else{
            message = 'Il y a des erreurs dans le formulaire';
            $('.message'+idBloc).html('').addClass('erreur').append(message);
        }
    });

    //Cookies
    if(Cookies.get('bandeauCookies') !== 'off'){
        $('.cookies').show();
    }

    $('#cookies-ok').click(function(e){
        e.preventDefault();
        Cookies.set('bandeauCookies', 'off');
        $('.cookies').hide();
    });

    //ScrollTop
    $('.scrollTop').click(function(){
        $('body, html').animate({
            scrollTop: 0
        }, 500)
    });

    $(window).on('scroll', function(){
        if($(window).width() > 576){
            hauteur = $(window).height();
            scroll = window.pageYOffset || document.documentElement.scrollTop;
            if(hauteur < scroll){
                $('.scrollTop').fadeIn();
            }else{
                $('.scrollTop').fadeOut();
            }
        }
    });

    $(window).on('resize orientationchange', function(){
        if($(window).width() > 576){
            hauteur = $(window).height();
            scroll = window.pageYOffset || document.documentElement.scrollTop;
            if(hauteur < scroll){
                $('.scrollTop').fadeIn();
            }else{
                $('.scrollTop').fadeOut();
            }
        }else{
            $('.scrollTop').show();
        }
    });

    //Burger
    $('#burger').click(function(){
        $(this).toggleClass('actif');
        $(this).find('svg').toggleClass('fa-bars fa-times');
    });

    $(window).on('resize orientationchange', function(){
        $('#burger').removeClass('actif').find('svg').addClass('fa-bars').removeClass('fa-times');
    });

    //Toggle formulaire ajout de commentaire
    $('.blocCommentaires-ajout').click(function(){
        $(this).find('svg').toggleClass('fa-angle-down fa-angle-up');
        $(this).next('div').slideToggle();
    });

    //Bloc accordéon
    $('.blocAccordeon-titre').click(function(){
        $(this).closest('.blocAccordeon-section').toggleClass('actif');
        $(this).next('.blocAccordeon-texte').slideToggle();
    });

    //Recherche du bloc LEI
    $('.blocLEI-recherche--critere').click(function(){
        blocLEI = $(this).closest('.blocLEI');
        boutonTous = blocLEI.find('.blocLEI-recherche--critere[data-critere="tous"]');
        fiches = blocLEI.find('.ficheLEI');
        
        if($(this).data('critere') === 'tous' && !$(this).hasClass('actif')){//Tout afficher
            blocLEI.find('.blocLEI-recherche--critere').removeClass('actif');
            $(this).addClass('actif');
            fiches.show();
        }else if($(this).data('critere') !== 'tous'){//"Tous"
            if($(this).hasClass('actif')){//Désactiver le critère
                fiches.hide();
                blocLEI.find('.blocLEI-recherche--critere.actif').not($(this)).each(function(){
                    blocLEI.find('.ficheLEI[data-criteres*="'+$(this).data('critere')+'"]').show();
                });
                if(blocLEI.find('.blocLEI-recherche--critere.actif').length < 2){//Dernier critère désactivé -> Tous
                    boutonTous.addClass('actif');
                    fiches.show();
                }
            }else{//Activer le critère
                if(boutonTous.hasClass('actif')){//Bouton "Tous" actif : premier critère activé
                    boutonTous.removeClass('actif');
                    fiches.hide();
                }
                blocLEI.find('.ficheLEI[data-criteres*="'+$(this).data('critere')+'"]').show();
            }
            $(this).toggleClass('actif');
        }
    });

    //Marquage de la page active dans les menus
    idPage = $('main').attr('id').replace('page', '');
    $('a[data-idpage="'+idPage+'"]').addClass('pageActive').parents('li').addClass('menuActif');
});