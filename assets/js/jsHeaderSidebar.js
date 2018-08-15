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
            idPage = $('form').attr('data-entity-id');
        }
        $('.page#'+idPage).parent('a').addClass('page-active');
        $('.page#'+idPage).parents('.jstree').find('li[id$="_1"] > a').addClass('page-active');
    };

    $('.sidebar-menus div[id^="menu"]').on('ready.jstree open_node.jstree move_node.jstree', surbrillancePageActive);

    /* Changement de la langue de l'arbo */
    if($.cookie('langueArbo') === null){
        $.cookie('langueArbo', '1');
    }

    $('.arbo-langues a.'+$.cookie('langueArbo')).closest('span').addClass('current');

    $('.arbo-langues a').click(function(){
        idLangue = $(this).attr('class');
        $.cookie('langueArbo', idLangue);
        location.reload();
    })
});