$(document).ready(function() {
    /* Une seule langue par défaut */
    $('body.list-langue td[data-label="Defaut"] input').click(function(){
        if($(this).is(':checked')){
            $('body.list-langue td[data-label="Defaut"] input').not(this).attr("checked", false);
        }
    });
});