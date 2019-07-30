$(document).ready(function() {
    // Installation
    $('.installation-theme').click(function(){
        lien = $(this).closest('.theme-actions').data('lien');
        nom = $(this).closest('.theme-actions').data('nom');
        bouton = $(this);
        $('.messages .loader').show();
        $.ajax({
            url: Routing.generate('installerTheme'),
            method: "post",
            data: {lien: lien, nom: nom}
        })
            .done(function(){
                $('.messages .loader').hide();
                $('.message-ok').empty().append('<i class="fas fa-check-circle"></i>Le thème a été installé').fadeIn().delay(800).fadeOut();

                bouton.hide();
                bouton.siblings('.activation-theme').show();
                bouton.siblings('.desinstallation-theme').show();
            })
            .fail(function(){
                $('.message-fail').fadeIn().delay(800).fadeOut();
            });
    });

    //Activation
    $('.theme-actions').on('click', '.activation-theme', function(){
        theme = $(this).closest('.theme-actions').data('nom');
        bouton = $(this);
        $('.messages .loader').show();
        $.ajax({
            url: Routing.generate('changerTheme'),
            method: "post",
            data: {theme: theme}
        })
            .done(function(){
                $('.messages .loader').hide();
                $('.message-ok').empty().append('<i class="fas fa-check-circle"></i>Le thème a été activé').fadeIn().delay(800).fadeOut();

                nomAncienTheme = $('.theme.actif').attr('id');
                $('.theme.actif').find(".activation-theme").show();
                // $('.theme.actif').find(".parametrage-theme").hide();
                lien = $('.theme.actif').find(".theme-actions").data("lien");
                if(typeof lien !== typeof undefined && lien !== false){
                    $('.theme.actif').find(".desinstallation-theme").show();
                }

                $('.theme').removeClass('actif');
                $('#'+theme).addClass('actif');

                bouton.hide();
                bouton.siblings('.desinstallation-theme').hide();
                // bouton.siblings('.parametrage-theme').show();
            })
            .fail(function(){
                $('.message-fail').fadeIn().delay(800).fadeOut();
            });
    });

    //Désinstallation
    $('.theme-actions').on('click', '.desinstallation-theme', function(){
        nom = $(this).closest('.theme-actions').data('nom');
        lien = $(this).closest('.theme-actions').data('lien');
        bouton = $(this);
        $('.messages .loader').show();
        $.ajax({
            url: '/admin/theme/desinstaller',
            method: "post",
            data: {nom: nom}
        })
            .done(function(data){
                $('.messages .loader').hide();
                $('.message-ok').empty().append('<i class="fas fa-check-circle"></i>Le thème a été désinstallé').fadeIn().delay(800).fadeOut();

                bouton.hide();
                bouton.siblings('.activation-theme').hide();
                bouton.siblings('.installation-theme').show();
            })
            .fail(function(){
                $('.message-fail').fadeIn().delay(800).fadeOut();
            });
    });

    //Réinitialisation d'un paramètre
    $('.reinitialisationParametres').click(function(e){
        e.preventDefault();
        valeur = $(this).data('parametre-defaut');
        $(this).prev('input').val(valeur);
        $(this).attr('disabled', true);
    });

    $('.parametrageTheme input').change(function(){
        $(this).next('.reinitialisationParametres').attr('disabled', false);
    });
});