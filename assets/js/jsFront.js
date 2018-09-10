$(document).ready(function(){
    $('.blocSlider').each(function(){
        idSlider = $(this).children('div').attr('id');
        $('#'+idSlider).slick({
            "arrows": true,
            "dots": true
        });
    });
});