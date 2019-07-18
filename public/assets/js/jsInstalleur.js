$(document).ready(function(){
    $('#testConnexion').click(function(){
        $.ajax({
            url: '/installeur/0',
            method: "post",
            data: {form: $('form').serializeArray()}
        })
            .done(function(data){
                console.log(data);
                if(data === 'ok'){
                    resultat = 'fa-check';
                }else{
                    resultat = 'fa-times';
                }

                $('#resultatTestConnexion').html('<i class="fas '+resultat+'"></i>');
            })
            .fail(function(){
                resultat = 'fa-times';
                $('#resultatTestConnexion').html('<i class="fas '+resultat+'"></i>');
            });
    });

    $('.theme').click(function(){
        $('.theme').not($(this)).removeClass('actif');
        $(this).addClass('actif');
        theme = $(this).attr('id');
        $('#form_theme').val(theme);
    });

    $('#ajoutPageHeader, #ajoutPageFooter').click(function(){
        menu = $(this).data('menu');
       $(this).prev('div').append('<p><input type="text"><button class="ajoutPage" data-menu="'+menu+'">Enregistrer</button></p>');
    });

    $('#creationPages').on('click', '.ajoutPage', function(){
        titre = $(this).prev('input').val();
        menu = $(this).data('menu');
        if(titre !== ''){
            $(this).hide().closest('p').append('<i class="fas fa-sync fa-spin"></i>');
            elem = $(this);
            $.ajax({
                url: '/installeur/8',
                method: "post",
                data: {titre: titre, menu: menu}
            })
                .done(function(data){
                    if(data === 'ok'){
                        resultat = 'fa-check';
                    }else{
                        resultat = 'fa-times';
                    }

                    elem.closest('p').find('svg').remove();
                    elem.closest('p').append('<i class="fas '+resultat+'"></i>');

                    setTimeout(function(){
                        elem.closest('p').html(titre);
                    }, 1000);
                })
                .fail(function(){
                    resultat = 'fa-times';

                    elem.closest('p').find('svg').remove();
                    elem.closest('p').append('<i class="fas '+resultat+'"></i>');
                });
        }
    });
});