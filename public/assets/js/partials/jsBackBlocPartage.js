$(document).ready(function() {
    /* Changement du h1 lors de l'édition d'un bloc partagé */
    if ($('#blocpartage_nom').length > 0) {
        $('h1.title').html('Bloc partagé <i>' + $('#blocpartage_nom').val() + '</i>');
    }

    $('#blocpartage_nom').on('keyup', function () {
        $('h1.title').html('Bloc partagé <i>' + $(this).val() + '</i>');
    });

    //Création d'un bloc partagé : choix du type de bloc
    $('#blocpartage_typeBloc').on('change', function(){
        type = $(this).val();

        entite = $('.listeBlocs').siblings('form').attr('name');

        $.ajax({
            url: Routing.generate('ajouterBloc'),
            method: "post",
            data: {type: type, typeBloc: 'Bloc'}
        })
            .done(function(data){
                $('div[id^=nvBloc]').attr('id', '');

                saveCloseFormulaire();

                var form = data.replace(/bloc_/g, entite+'_bloc_')
                    .replace(/bloc\[/g, entite+'[bloc][');

                bloc = '<div id="nvBloc'+count+'" class="form-group field-bloc nvBloc col12 bloc-'+type.toLowerCase()+'" data-name="0">'+form+'</div>';

                $('.field-bloc').replaceWith(bloc);

                nvBloc = $('#nvBloc' + count);

                nvBloc.resizable(optionsResizable).resizable( "option", "maxWidth", 992 );

                nvBloc.find('input[name$="[largeur]"]').val('col12');

                nvBloc.find('.bloc-panel.bloc-formulaire').removeClass('hidden');

                tinymce.remove();
                tinymce.init(optionsTinyMCEParagraphe);
                tinymce.init(optionsTinyMCE);
                $('.select-multiple').select2();
                $(".dndBlocs").sortable(options);
                $(".dndBlocs.noDrag").sortable("disable");
            });

        $('.field-choix_type_bloc').addClass('hidden');
        $('.noDrag').show();

        if(type === 'Section'){
            $('#toggleConteneurs, #toggleListeBlocsDnD').removeClass('hidden');
            $('.listeBlocsDnD').show();
        }
    });

    //Erreur si on ne choisit pas de type de bloc
    $("#new-blocpartage-form .formulaire-actions-enregistrer").click(function(e) {
        e.preventDefault();

        $('.error-block').remove();

        if($('#blocpartage_typeBloc').val() === ''){
            $('.field-choix_type_bloc').append('<div class="error-block"><span>Erreur :</span> Vous devez choisir un type de bloc</div>');
        }else{
            $("#new-blocpartage-form").submit();
        }
    });
});