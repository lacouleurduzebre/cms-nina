$(document).ready(function(){
    //Clic sur élément de menu parent
    $('li.parent').click(function(e){
        e.stopPropagation();
        $(this).toggleClass('actif');
    });

    //Stop propagation sur liens
    $('.blocMenu a').click(function(e){
        e.stopPropagation();
    });
});