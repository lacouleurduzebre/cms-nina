$(document).ready(function () {
    // Installation
    $('.theme-actions').on('click', '.installation-theme', function () {
        lien = $(this).closest('.theme-actions').data('lien');
        nom = $(this).closest('.theme-actions').data('nom');
        bouton = $(this);
        blocTheme = $('#' + nom);

        $('#' + nom + ' .theme-messages').css('display', 'flex');
        $('#' + nom + ' .loader').show();

        $.ajax({
            url: Routing.generate('installerTheme'),
            method: "post",
            data: {lien: lien, nom: nom}
        })
            .done(function () {
                $('#' + nom + ' .loader').hide();
                $('#' + nom + ' .message-ok').empty().append('<i class="fas fa-check-circle"></i>Le thème a été installé').fadeIn().delay(800).fadeOut();

                bouton.hide();
                bouton.siblings('.activation-theme').show();
                bouton.siblings('.desinstallation-theme').show();

                setTimeout(function () {
                    $('#' + nom + ' .theme-messages').hide();
                    blocTheme.appendTo('.themesInstalles');
                }, 1600);
            })
            .fail(function () {
                $('#' + nom + ' .message-fail').fadeIn().delay(800).fadeOut();
            });
    });

    //Activation
    $('.theme-actions').on('click', '.activation-theme', function () {
        theme = $(this).closest('.theme-actions').data('nom');
        nom = $(this).closest('.theme-actions').data('nom');
        bouton = $(this);
        blocTheme = $('#' + nom);

        $('#' + nom + ' .theme-messages').css('display', 'flex');
        $('#' + nom + ' .loader').show();

        $.ajax({
            url: Routing.generate('changerTheme'),
            method: "post",
            data: {theme: theme}
        })
            .done(function () {
                $('#' + nom + ' .loader').hide();
                $('#' + nom + ' .message-ok').empty().append('<i class="fas fa-check-circle"></i>Le thème a été activé').fadeIn().delay(800).fadeOut();

                nomAncienTheme = $('.theme.actif').attr('id');
                $('.theme.actif').find(".activation-theme").show();
                // $('.theme.actif').find(".parametrage-theme").hide();
                lien = $('.theme.actif').find(".theme-actions").data("lien");
                if (typeof lien !== typeof undefined && lien !== false) {
                    $('.theme.actif').find(".desinstallation-theme").show();
                }

                $('.theme').removeClass('actif');
                $('#' + theme).addClass('actif');

                bouton.hide();
                bouton.siblings('.desinstallation-theme').hide();
                // bouton.siblings('.parametrage-theme').show();

                setTimeout(function () {
                    $('#' + nom + ' .theme-messages').hide();
                }, 1600);
            })
            .fail(function () {
                $('#' + nom + ' .message-fail').fadeIn().delay(800).fadeOut();
            });
    });

    //Désinstallation
    $('.theme-actions').on('click', '.desinstallation-theme', function () {
        nom = $(this).closest('.theme-actions').data('nom');
        lien = $(this).closest('.theme-actions').data('lien');
        bouton = $(this);
        blocTheme = $('#' + nom);

        $('#' + nom + ' .theme-messages').css('display', 'flex');
        $('#' + nom + ' .loader').show();

        $.ajax({
            url: '/admin/theme/desinstaller',
            method: "post",
            data: {nom: nom}
        })
            .done(function (data) {
                $('#' + nom + ' .loader').hide();
                $('#' + nom + ' .message-ok').empty().append('<i class="fas fa-check-circle"></i>Le thème a été désinstallé').fadeIn().delay(800).fadeOut();

                bouton.hide();
                bouton.siblings('.activation-theme').hide();
                bouton.siblings('.installation-theme').show();

                setTimeout(function () {
                    $('#' + nom + ' .theme-messages').hide();
                    blocTheme.appendTo('.themesDisponibles');
                }, 1600);
            })
            .fail(function () {
                $('#' + nom + ' .message-fail').fadeIn().delay(800).fadeOut();
            });
    });

    //Paramétrage
    $('input[type="color"]').change(function () {
        couleur = $(this).val();
        $('.echantillonCouleur[data-champ="' + $(this).attr('name') + '"]').css('background-color', couleur);
    });

    $('.elementParametrable').click(function(e){
        e.preventDefault();

        //Masquer les autres paramètres
        $('.elementParametrable').not(this).removeClass('actif');
        parametres = $(this).data('parametres');
        $('.parametres').not('.'+parametres).hide();

        //Afficher / masquer les paramètres de l'élément
        $(this).toggleClass('actif');
        $('.parametres.'+parametres).toggle();
    });
    /*$('.field-polices select').change(function () {
        polices = $(this).val();
        html = '';
        polices.forEach(function (item) {
            html += '<option value="' + item + '">' + item + '</option>';
        });
        $('.field-choix_police').each(function () {
            $(this).find('select').html(html);
        });
    });*/
});