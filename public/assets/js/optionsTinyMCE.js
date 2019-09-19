$(document).ready(function(){
    optionsTinyMCE = {
        selector: "textarea:not('.notTinymce')",
        language: "fr_FR",
        theme: "modern",
        branding: false,
        height: 300,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
            "table contextmenu directionality emoticons paste textcolor responsivefilemanager code imagetools"
        ],
        relative_urls: false,
        menubar: false,

        filemanager_title:"Médiathèque",
        external_filemanager_path:"/filemanager/",
        external_plugins: { "filemanager" : "/filemanager/plugin.min.js"},

        extended_valid_elements: 'i[class=class]',

        block_formats: 'Paragraphe=p;Titre h2=h2;Titre h3=h3;Titre h4=h4;Titre h5=h5;Titre h6=h6',
        image_advtab: true,
        toolbar1: "formatselect | image | media | link unlink | copy paste pastetext | bold italic underline | alignleft aligncenter alignright | bullist numlist | code | undo redo",

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                $('.formulaire-actions-enregistrer').attr("disabled", false);
                $(window).bind('beforeunload', function(){
                    if(!clicEnregistrement){
                        return 'Êtes-vous sûr de vouloir quitter cette page ? Des données pourraient ne pas avoir été enregistrées';
                    }
                });
            });
        },

        //Affichage de la barre au clic
        setup: function (editor) {
            editor.on('focus', function () {
                $(this.contentAreaContainer.parentElement).find("div.mce-flow-layout").show();
            });
            editor.on('blur', function () {
                $(this.contentAreaContainer.parentElement).find("div.mce-flow-layout").hide();
            });
            editor.on('init', function() {
                $(this.contentAreaContainer.parentElement).find("div.mce-flow-layout").hide();
            });
        }
    };
});