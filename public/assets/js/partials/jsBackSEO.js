$(document).ready(function() {
    //Édition
    $('.listeSEO-action-edition').click(function(){
        id = $(this).closest('.listeSEO-SEO').data('id');

        $.ajax({
            url: '/admin/seo/edition',
            method: 'POST',
            data:{
                id: id
            }
        }).done(function(data){
            champs = $('.listeSEO-edition[data-id="'+id+'"]');
            formulaire = champs.find('.listeSEO-formulaire');
            formulaire.html(data);
            hauteurTitre = $('.listeSEO-SEO[data-id="'+id+'"]').find('.titre').height()+16
            champs.show().css('top', hauteurTitre);
            champs.closest('.listeSEO-SEO').height($('.listeSEO-edition[data-id="'+id+'"]').height()+hauteurTitre);
        });
    });

    //Enregistrer
    $('.listeSEO-edition').on('click', '.listeSEO-action-enregistrer', function(){
        $(this).closest('.listeSEO-SEO').addClass('chargement');
        id = $(this).closest('.listeSEO-SEO').data('id');
        formulaire = $(this).closest('div').siblings('.listeSEO-formulaire').find('form');

        $.ajax({
            url: '/admin/seo/edition',
            method: 'POST',
            data:{
                id: id,
                donnees: formulaire.serializeArray()
            }
        }).done(function(data){
            conteneur = $('.listeSEO-SEO[data-id="'+id+'"]');
            conteneur.removeClass('chargement').find('.listeSEO-apercu').html(data);
            conteneur.find('.listeSEO-edition').hide();
            conteneur.height('auto');
        });
    });

    //Annuler
    $('.listeSEO-edition').on('click', '.listeSEO-action-annuler', function(){
        $(this).closest('.listeSEO-edition').hide();
        $(this).closest('.listeSEO-SEO').height('auto');
    });

    //Modif url
    $('.listeSEO-edition').on('keyup', 'input[name="seo[url]"]', function(){
        urlNonFormattee = $(this).val();
        url = str2url(urlNonFormattee, 'UTF-8', true);
        $(this).val(url);
    });

    scoreSEOPageReferencement= function(element, limite, classProgression){
        longueur = element.val().length;

        if(longueur < (limite/3) || longueur > limite){
            nvClass = 'danger';
        }else if(longueur > (limite/3)*2){
            nvClass = 'success';
        }else{
            nvClass = 'warning';
        }

        progression = element.closest('.listeSEO-edition').find(classProgression);
        progression.attr('title', longueur+' / '+limite);
        progression.removeClass('warning danger success').addClass(nvClass);
    };

    $('.listeSEO-edition').on('change keyup', '#seo_metaTitre', function(){
        scoreSEOPageReferencement($(this), 65, '.progression-metaTitre');
    });

    $('.listeSEO-edition').on('change keyup', '#seo_url', function(){
        scoreSEOPageReferencement($(this), 75, '.progression-url');
    });

    $('.listeSEO-edition').on('change keyup', '#seo_metaDescription', function(){
        scoreSEOPageReferencement($(this), 150, '.progression-metaDescription');
    });

    //Voir la page
    $('.listeSEO-action-voirPage').click(function(){
        Cookies.set('ongletActif', '_easyadmin_form_design_element_4-tab', { expires: 7 });
    });

    //Réinitialisation
    $('.listeSEO-action-raz').click(function(){
        $(this).closest('.listeSEO-SEO').addClass('chargement');
        id = $(this).closest('.listeSEO-SEO').data('id');

        $.ajax({
            url: '/admin/seo/raz',
            method: 'POST',
            data:{
                id: id
            }
        }).done(function(data){
            $('.listeSEO-SEO[data-id="'+id+'"]')
                .removeClass('chargement')
                .find('.listeSEO-apercu').html(data);
        });
    });
});