$(document).ready(function() {
    //Activation/désactivation
    $('.configBlocs-bloc-actif').on('change', function(){
        checkbox = $(this);
        actif = $(this).is(':checked');
        type = $(this).closest('tr').attr('id');
        typeBloc = $(this).closest('table').attr('id');
        $.ajax({
            url: window.location.href,
            data: {action: 'actif', typeBloc: typeBloc, type: type, actif: actif}
        })
            .fail(function(){
                checkbox.attr('checked', !actif);
                checkbox.attr('disabled', true);
            })
    });

    //Changement de priorité
    $(".configBlocs tbody").sortable({
        handle: '.dragConfigBloc',
        update: function(event, ui){
            blocs = {};
            typeBloc = $(this).closest('table').attr('id');
            $(this).closest('table').find('tbody tr').each(function(){
                type = $(this).attr('id');
                priorite = $(this).index() + 1;
                blocs[type] = priorite;
            });
            $.ajax({
                url: window.location.href,
                data: {action: 'priorite', blocs: blocs, typeBloc: typeBloc}
            })
                .done(function(){
                    $('.alert-enregistrement').show();

                })
                .fail(function(){
                    $('.content-wrapper').prepend('<p>Une erreur est survenue</p>');
                })
        }
    });
});