$(document).ready(function() {
    //Ajout de rôle : uppercase
    $('#creationRole input[type=text]').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    });

    //Suppression de rôle
    $('.suppressionRole').click(function(){
        if(confirm('Supprimer le rôle "'+$(this).prev('span').html()+'" ?')){
            $.ajax({
                url: '/admin/droits/suppressionRole',
                method: 'POST',
                data: {idRole: $(this).data('role')}
            }).done(function(data){
                if(data === 'ok'){
                    window.location = window.location.href;
                }
            })
        }
    });
});