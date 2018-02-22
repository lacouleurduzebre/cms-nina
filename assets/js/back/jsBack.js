$(document).ready(function(){
    $('#btnArbo').click(function(){
        $(this).parent('.arbo-titre').toggleClass('active').toggleClass('inactive');
        $(this).parents('header').siblings('div').slideToggle();
    });
});