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

    $('.guideStyle').on('click', '.elementParametrable', function(e){
        e.preventDefault();

        //Masquer les autres paramètres
        $('.elementParametrable').not(this).removeClass('actif');
        parametres = $(this).data('parametres');
        $('.parametres').not('[data-parametres="'+parametres+'"]').hide();

        //Afficher / masquer les paramètres de l'élément
        $(this).toggleClass('actif');
        $('.parametres[data-parametres="'+parametres+'"]').toggle();
    });

        //Color picker logo
    dec2hex = function(i){
        var hex = "00";
        if   (i >= 0  && i <= 15)  { hex = "0" + i.toString(16); }
        else if (i >= 16  && i <= 255)  { hex = i.toString(16); }
        return hex;
    };

    hex2rgb = function(hex){
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    };

    selectionCouleur = function(e) {
        var x, y;
        var c = $('.parametrageTheme-selectionCouleur canvas').get(0);
        var cxt=c.getContext("2d");

        if (e.pageX || e.pageY) {
            x = e.pageX;
            y = e.pageY;
        } else {
            x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
            y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
        }
        var element = c.offsetParent;
        while (element !== null) {
            x = parseInt(x) - parseInt(element.offsetLeft);
            y = parseInt(y) - parseInt(element.offsetTop);
            element = element.offsetParent;
        }

        x -= c.offsetLeft;
        y -= c.offsetTop;

        var cData = cxt.getImageData(x, y, 3, 3).data;
        var mr = Math.round((cData[0] + cData[4] + cData[8] + cData[12] + cData[16] + cData[20] + cData[24] + cData[28] + cData[32]) / 9);
        var mg = Math.round((cData[1] + cData[5] + cData[9] + cData[13] + cData[17] + cData[21] + cData[25] + cData[29] + cData[33]) / 9);
        var mb = Math.round((cData[2] + cData[6] + cData[10] + cData[14] + cData[18] + cData[22] + cData[26] + cData[30] + cData[34]) / 9);
        var hex = dec2hex(mr) + dec2hex(mg) + dec2hex(mb);

        selecteur = e.type === "click" ? 'Selectionnee' : 'Survolee';
        $('.parametrageTheme-couleur'+selecteur+' .echantillon').css('background-color', '#' + hex);
        $('.parametrageTheme-couleur'+selecteur+' .codeHexa').html('#' + hex);
        $('.parametrageTheme-couleur'+selecteur+' .codeRGB').html('RGB (' + mr + ',' + mg + ',' + mb + ')');
    };

    if($('.parametrageTheme-logo').length > 0){
        image = $('.parametrageTheme-logo').get(0);
        var canvas = document.createElement("canvas");

        imageWidth = image.width;
        while(imageWidth === 0){
            setTimeout(function(){
                imageWidth = image.width;
            }, 500);
        }

        canvas.width = image.width;
        canvas.height = image.height;
        canvas.getContext("2d").drawImage(image, 0, 0);

        $('.parametrageTheme-selectionCouleur').prepend(canvas);

        $('.parametrageTheme-selectionCouleur canvas').on('mousemove click', function(e){
            selectionCouleur(e);
        });
    }

    //Styles de blocs : fond
    fondsStylesBlocs = function(){
        $('input[name^="parametrage_theme[stylesBlocs]"][name$="[opaciteFond]"]').each(function(){
            if($(this).val()){
                parametre = $(this).closest('.parametres').data('parametres');
                opacite = 0.01*$(this).val();

                idChampCouleurFond = $(this).attr('id').replace('opaciteFond', 'couleurFond');
                couleurFond = $('#'+idChampCouleurFond).val();
                if(couleurFond){
                    rgb = hex2rgb(couleurFond);
                    $('.elementParametrable[data-parametres="'+parametre+'"]').css('background-color', 'rgba('+rgb.r+','+rgb.g+','+rgb.b+','+opacite+')');
                }
            }
        })
    };
    fondsStylesBlocs();

    //Modification du guide de style en direct
    $('.guideStyle').on('keyup change', 'input, select', function(){
        if($(this).attr('name').indexOf('stylesBlocs') > 0 && $(this).attr('name').indexOf('Fond') > 0){
            fondsStylesBlocs();
        }else if($(this).closest('[data-propriete]').length > 0){
           propriete = $(this).closest('[data-propriete]').attr('data-propriete');
           valeur = $(this).val();
           parametre = $(this).closest('.parametres').data('parametres');

           //Si valeur = variable, on cherche la vraie valeur
           if(valeur.substr(0, 1) === '$'){
               nomVariable = valeur.substr(1, valeur.length - 1);
               valeur = $('[name="parametrage_theme['+nomVariable+']"]').val();
           }

           //Modification du guide de style
           $('.elementParametrable[data-parametres="'+parametre+'"]').css(propriete, valeur);
       }
    });

    //Modification du nom / fond d'un style de bloc
    $('.guideStyle').on('change', 'input[name^="parametrage_theme[stylesBlocs]"][name$="[nom]"]', function(){
        nom = $(this).val();
        $(this).closest('.elementParametrable-conteneur').find('.styleBloc-nom').html(nom);
    });
});