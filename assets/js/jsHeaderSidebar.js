$(document).ready(function(){
    /* Toggle colonne de gauche */
    if($.cookie('full') === 'on'){
        $('body').addClass('full');
    }else{
        $('body').addClass('notFull');
    }

    $('#btnArbo').click(function(e){
        e.preventDefault();
        $('body').toggleClass('full');
        $('body').toggleClass('notFull');
        $.cookie('full') === 'on' ? $.cookie('full', 'off') : $.cookie('full', 'on');
    });

    /* Page active colorée dans l'arbo */
    surbrillancePageActive = function(){
        if(!$('body.front').hasClass('connected') && !$('body.easyadmin').hasClass('edit-page_active')) {
            return false;
        }
        if($('body').hasClass('front')){
            idPage = $('main.page').attr('id');
        }else if($('body').hasClass('easyadmin')){
            idPage = $('#edit-page_active-form').attr('data-entity-id');
        }
        $('#'+idPage+'.page').closest('a').addClass('page-active');
        $('#'+idPage+'.page').parents('.jstree').find('li[id$="_1"] > a').addClass('page-active');
    };

    $('.sidebar-menus div[id^="menu"]').on('ready.jstree open_node.jstree move_node.jstree', surbrillancePageActive);

    /* Changement de la langue de l'arbo */
    $('.arbo-langues a').click(function(){
        idLangue = $(this).attr('class');
        $.cookie('langueArbo', idLangue);
        location.reload();
    });

    /* Menu contextuel de la page d'accueil */
    $("#pageAccueil-page").on("click contextmenu", function(e){
        e.preventDefault();
        $("#pageAccueil-menu").toggle();
        return false;
    });
});