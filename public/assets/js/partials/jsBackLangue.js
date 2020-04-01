$(document).ready(function() {
    /* Une seule langue par défaut */
    $('body.list-langue td[data-label="Defaut"] input').click(function(){
        if($(this).is(':checked')){
            $('body.list-langue td[data-label="Defaut"] input:checked').not(this).click();
        }

        if( $('body.list-langue').find('td[data-label="Defaut"] input:checked').length === 0){
            $(this).click();
        }
    });
});