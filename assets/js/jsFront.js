$(document).ready(function(){
    //Envoi des formulaires
    $('.blocFormulaire-formulaire').on('submit', function(e){
        e.preventDefault();

        idBloc = $(this).attr('id');
        donnees = $(this).serializeArray();

        $('.message'+idBloc).html('');

        message = '';

        $(this).find('label.required').each(function(){
            idChamp = $(this).attr('for');
            if($('#'+idChamp).val() === ''){
                message = message.concat('<p>Merci de compléter le champ "'+$(this).html()+'"</p>');
            }
        });

        if(message === ''){
            $.ajax({
                url: window.location.origin+"/admin/envoiMail",
                method: "post",
                data: {donnees: donnees, idBloc: idBloc}
            })
                .done(function(data){
                    $('.message'+idBloc).html(data);
                });
        }else{
            $('.message'+idBloc).addClass('error').append(message);
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

    //Toggle formulaire ajout de commentaire
    $('.blocCommentaires-ajout').click(function(){
        $(this).find('svg').toggleClass('fa-angle-down fa-angle-up');
        $(this).next('div').slideToggle();
    });
});