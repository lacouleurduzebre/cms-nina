$(document).ready(function(){
    /* Toggle colonne de gauche */
    if(Cookies.get('full') === 'on'){
        $('body').addClass('full');
    }else{
        $('body').addClass('notFull');
    }

    $('#btnArbo').click(function(e){
        e.preventDefault();
        $('body').toggleClass('full');
        $('body').toggleClass('notFull');
        Cookies.get('full') === 'on' ? Cookies.set('full', 'off', { expires: 7 }) : Cookies.set('full', 'on', { expires: 7 });
    });

    /* Page active colorée dans l'arbo */
    surbrillancePageActive = function(){
        if(!$('body.front').hasClass('connected') && !$('body.easyadmin').hasClass('edit-page_active')) {
            return false;
        }
        if($('body').hasClass('front')){
            idPage = $('main.page').attr('id').substr(4, $('main').attr('id').length);
            if($('body').hasClass('accueil')){
                $('.pageAccueil-page').addClass('page-active');
                return;
            }
        }else if($('body').hasClass('easyadmin')){
            idPage = $('#edit-page_active-form').attr('data-entity-id');
        }
        $('#'+idPage+'.page').closest('a').addClass('page-active');
        $('#'+idPage+'.page').parents('.jstree').find('li[id$="_1"] > a').addClass('page-active');
    };

    $('.sidebar-menus div[id^="menu"]').on('ready.jstree open_node.jstree move_node.jstree', surbrillancePageActive);

    /* Changement de la langue de l'arbo */
    $('.arbo-langues a').click(function(e){
        e.preventDefault();
        idLangue = $(this).attr('class');
        nomlangue = $(this).attr('id');
        Cookies.set('langueArbo', idLangue, { expires: 7 });
        location.href = $(this).attr('href');
    });

    /* Menu contextuel de la page d'accueil */
    $(".pageAccueil").on("click contextmenu", '.pageAccueil-page', function(e){
        e.preventDefault();
        $("#pageAccueil-menu").toggle();
        return false;
    });

    /* Filtre arbo */
    var typingTimer;                //timer identifier

    function rechercheArbo () {
        console.log('recherche');
        $("#recherche-arbo-chargement").removeClass('hidden');
        $("#recherche-arbo-loupe").addClass('hidden');
        $("#recherche-arbo-vidange").addClass('hidden');
        langue = $('.arbo-langues .current a').attr('class');
        recherche = $('#recherche-arbo').val();
        $.ajax({
            url: '/admin/menu/filtreArbo',
            method: "post",
            data: {langue: langue, recherche: recherche}
        })
            .done(function(data){
                $("#recherche-arbo-vidange").removeClass('hidden');
                $("#recherche-arbo-chargement").addClass('hidden');
                $('#recherche-arbo-resultats').html('').append(data).show();
                $('.arborescence').hide();
            })
            .fail(function(){
                $("#recherche-arbo-vidange").removeClass('hidden');
                $("#recherche-arbo-chargement").addClass('hidden');
                $('#recherche-arbo-resultats').html('').append('Une erreur est survenue').show();
            });
    }

    $('#recherche-arbo').on('keyup', function(){
        clearTimeout(typingTimer);
        typingTimer = setTimeout(rechercheArbo, 600);
    })
        .on('click', function(e){//Bloque le lancement de la vidange en cliquant sur le champ
           e.stopPropagation();
        });

        /* Vidange */
        $('.sidebar.global-actions').click('#recherche-arbo-vidange', function(){
            $('#recherche-arbo-resultats').html('').hide();
            $('.arborescence').show();
            $('#recherche-arbo').val("");
            $('#recherche-arbo-vidange').addClass('hidden');
            $('#recherche-arbo-loupe').removeClass('hidden');
        });

    /* Visualisation des blocs */
    $('#btn-toggle-blocs').click(function(e){
        e.preventDefault();
        $(this).toggleClass('actif');

        //Groupes de blocs
        $('.groupeBlocs').each(function(){
            //Position static
            if($(this).css('position') === 'static'){
                $(this).css('position', 'relative');
            }
            
            if($(this).find('.surbrillance').length > 0){
                $(this).toggleClass('surbrillanceOff');
            }else{
                $(this).append('<div class="surbrillance"><a href="/admin/?action=edit&entity=GroupeBlocs&id='+$(this).attr('id')+'"><i class="fas fa-pencil-alt"></i></a></div>');
            }
        });

        //Blocs de la page
        $('main > div[class^="bloc"]').each(function(){
            //Position static
            if($(this).css('position') === 'static'){
                $(this).css('position', 'relative');
            }

            if($(this).find('.surbrillance').length > 0){
                $(this).toggleClass('surbrillanceOff');
            }else{
                idPage = $('main').attr('id').substr(4, $('main').attr('id').length);
                $(this).append('<div class="surbrillance"><a href="/admin/?action=edit&entity=Page_Active&id='+idPage+'&blocActif='+$(this).data('bloc')+'"><i class="fas fa-pencil-alt"></i></a></div>');
            }
        });

        //Hover
        $('.surbrillance a').hover(function(){
            $(this).closest('.surbrillance').addClass('focus');
        },
        function(){
            $(this).closest('.surbrillance').removeClass('focus');
        });
    });
});