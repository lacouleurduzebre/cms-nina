$(document).ready(function(){
    $('#testConnexion').click(function(){
        console.log($('form').serializeArray());
        $.ajax({
            url: '/installeur/0',
            method: "post",
            data: {form: $('form').serializeArray()}
        })
            .done(function(data){
                console.log(data);
                if(data === 'ok'){
                    resultat = 'fa-check';
                }else{
                    resultat = 'fa-times';
                }

                $('#resultatTestConnexion').html('<i class="fas '+resultat+'"></i>');
            })
            .fail(function(){
                resultat = 'fa-times';
                $('#resultatTestConnexion').html('<i class="fas '+resultat+'"></i>');
            });
    });
});