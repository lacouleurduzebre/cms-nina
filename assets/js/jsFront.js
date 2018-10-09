$(document).ready(function(){
    //Envoi des formulaires
    $('.blocFormulaire-formulaire').on('submit', function(e){
        e.preventDefault();

        idBloc = $(this).attr('id');
        donnees = $(this).serializeArray();

        $('.message'+idBloc).html('');

        // console.log(donnees);

        message = '';

        console.log(donnees);
        $(this).find('label.required').each(function(){
            // console.log($(this).html());
            for (var i=0; i < donnees.length; i++) {
                if (donnees[i].name === $(this).html()) {
                    if(donnees[i].value !== ""){
                        return true;
                    }else{
                        break;
                    }
                }
            }
            message = message.concat('<p>Merci de compléter le champ "'+$(this).html()+'"</p>');
        });

        if(message === ''){
            console.log('formulaire OK');
            $.ajax({
                url: window.location.origin+"/admin/envoiMail",
                method: "post",
                data: {donnees: donnees, idBloc: idBloc}
            })
                .done(function(data){
                    console.log(data);
                });
        }else{
            console.log('formulaire pas OK');
            $('.message'+idBloc).addClass('error').append(message);
        }
    });

    //Cookies
    if($.cookie('bandeauCookies') !== 'off'){
        $('.cookies').show();
    }

    $('#cookies-ok').click(function(e){
        e.preventDefault();
        $.cookie('bandeauCookies', 'off');
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
});