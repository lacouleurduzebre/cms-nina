$(document).ready(function() {
    //Enregistrement des traductions en ajax
    $('.traductionTemplate').click(function(e){
        e.preventDefault();
        fichier = $(this).attr('data-fichier');
        $('.conteneurChargement').addClass('actif');
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data:{
                fichier: fichier,
                segments: $('form[data-fichier="'+fichier+'"]').serializeArray()
            }
        }).done(function(data){
            $('.conteneurChargement').removeClass('actif');
            $('.alert-enregistrement').show().delay(5000).fadeOut();
        });
    });

    //Copie des segments sources (traduction templates)
    $('.traductionTemplate-copie').click(function(){
        if(confirm('La valeur de tous les champs sera écrasée')){
            $(this).next('form').find('label').each(function(){
                valeur = $(this).html();
                $(this).next('input').val(valeur);
            });
        }
    });
});