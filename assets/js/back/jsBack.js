$(document).ready(function(){
    //Survol menu horizontal
    $('.sidebar-menu > li').mouseenter(function(){
        $(this).children('ul').show();
    });
    $('.sidebar-menu > li').mouseleave(function(){
        $(this).children('ul').hide();
    });

    //Changer l'orientation du menu du haut
    $('.topbar-toggle').click(function(){
        $(this).siblings('nav').toggleClass('horizontal').toggleClass('vertical');
        $('.content-wrapper').toggleClass('menu-vertical');
        if($('.content-wrapper').hasClass('menu-vertical')){
            Cookies.set('top-menu', 'vertical');
            $(this).find('svg').attr('data-icon', 'chevron-circle-right');
        }else{
            Cookies.set('top-menu', 'horizontal');
            $(this).find('svg').attr('data-icon', 'chevron-circle-left');
        }
    });

    $('header + .sidebar-toggle').on('click', function () {
        $('.navbar-static-top').toggleClass('arbo');
        $('.content-wrapper').toggleClass('arbo');
        if($('.navbar-static-top').hasClass('arbo')){
            Cookies.set('arbo', 'true');
            $(this).find('svg').attr('data-icon', 'chevron-circle-left');
        }else{
            Cookies.set('arbo', 'false');
            $(this).find('svg').attr('data-icon', 'chevron-circle-right');
        }
    });

    //Enregistrer la page en cours d'édition avec le bouton en haut de formulaire
    $('#enregistrerPage').click(function(e){
        e.preventDefault();
        $('#enregistrerPageBtn').click();
    });

    //Initialisation CKEditor
    ClassicEditor
        .create( document.querySelector( '.ckeditor' ) )
        .catch( error => {
            console.error( error );
        });
});

/*
document.addEventListener('DOMContentLoaded', function () {
    $('header + .sidebar-toggle').on('click', function () {
        console.log('clic');
        $(this)
            .find('[data-fa-processed]')
            .toggleClass('fa-chevron-circle-left')
            .toggleClass('fa-chevron-circle-right');
    });
});*/
