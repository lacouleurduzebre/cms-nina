$(document).ready(function() {
    //Ajout de rôle : uppercase
    $('#creationRole input[type=text]').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    })
});