$(document).ready(function(){

    $('nav li').mouseenter(function() {
        $(this).children('ul').show();
    });
    $('nav li').mouseleave(function() {
        $(this).children('ul').hide();
    });


    $('.btnMenuBack').click(function(){
        $(this).siblings().toggle();
        $(this).children('.fa').toggleClass('fa-arrow-left').toggleClass('fa-arrow-right');
    });

});