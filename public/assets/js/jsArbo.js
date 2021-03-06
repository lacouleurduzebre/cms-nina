$(document).ready(function(){
    var baseURL = window.location.origin;
    function menuContextuel(node){
        var items = {
            "edit":{
                "icon": "fa fa-pencil-alt",
                "label": "Éditer",
                "action": function(){
                    if(node.type === 'root' || node.type === 'orphan'){//Si Menu
                        idMenu = $('#'+node.id).parents('div').attr('id').substr(5);
                        window.location.href = baseURL+Routing.generate('easyadmin', { action: 'edit', entity: 'Menu', id: idMenu });
                    }else{//Si Page
                        idPage = $('#'+node.id).find('.page').attr('id');
                        window.location.href = baseURL+Routing.generate('easyadmin', { action: 'edit', entity: 'Page_Active', id: idPage });
                    }
                }
            },
            "see":{
                "icon": "fa fa-eye",
                "label": "Voir",
                "action": function(){
                    idPage = $('#'+node.id).find('.page').attr('id');

                    $.ajax({
                        url: baseURL+Routing.generate('urlPage'),
                        method: "post",
                        data: {idPage: idPage}
                    })
                        .done(function(data){
                            window.location.href = baseURL+"/"+data;
                        })
                }
            },
            "create":{
                "icon": "fa fa-plus",
                "label": "Ajouter une page enfant",
                "action": function(){
                    if(node.type === 'root' || node.type === 'orphan'){//Si Menu
                        idParent = null;
                    }else{//Si Page
                        idParent = $('#'+node.id).find('.menuPage').attr('id');
                    }
                    idMenu = $('#'+node.id).parents('div').attr('id').substr(5);

                    idMenuComplet = $('#'+node.id).parents('div').attr('id');

                    idLangue = $(".arbo-langues .current a").attr("class");

                    //Pop-up
                    $.get(baseURL+"/assets/js/popup-ajoutPage.html", function(data){
                        $('body').append(data);
                        $('#ajoutPage-idNode').val(node.id);
                        $('#ajoutPage-idParent').val(idParent);
                        $('#ajoutPage-idMenu').val(idMenu);
                        $('#ajoutPage-idMenuComplet').val(idMenuComplet);
                        $('#ajoutPage-idLangue').val(idLangue);
                    });
                }
            },
            "home":{
                "icon": "fa fa-home",
                "label": "Définir comme page d'accueil",
                "action": function(){
                    nomPage = $('#'+node.id+'_anchor').get(0).innerText;
                    if(confirm("Définir la page \""+nomPage+"\" comme page d'accueil ?")){
                        idPage = $('#'+node.id).find('.page').attr('id');
                        idMenuComplet = $('#'+node.id).parents('div').attr('id');

                        $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-sync fa-spin'></i>");

                        $.ajax({
                            url: baseURL+Routing.generate('definirPageAccueil'),
                            method: "post",
                            data: {idPage: idPage}
                        })
                            .done(function(data){
                                $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-check'></i>").delay(600).fadeOut();

                                $('li[data-jstree="{\\"type\\":\\"home\\"}"]').find('svg').removeClass('fa-home').addClass('fa-file');
                                $('li[data-jstree="{\\"type\\":\\"home\\"}"]').attr('data-jstree', '');

                                $('#'+node.id).attr('data-jstree', '{"type":"home"}');
                                $('#'+node.id).find('svg').removeClass('fa-file').addClass('fa-home');

                                //Changement du lien pour éditer la page d'accueil au dessus de l'arbo
                                $('#pageAccueil-menu > li:first-child > a').attr('href', '/admin/?entity=Page_Active&action=edit&id='+idPage);
                                $('.pageAccueil-titre').html(data);
                                if($('.pageAccueil').find('a').hasClass('sansPageAccueil')){
                                    $('.pageAccueil > a').attr('class', 'pageAccueil-page');
                                    $('.pageAccueil p').hide();
                                }
                            })
                            .fail(function(){
                                $('#loader-arbo.'+idMenuComplet).html("<i class='fas fa-times'></i>").delay(600).fadeOut();
                            });
                    }
                }
            },
            "alias":{
                "icon": "fa fa-file-export",
                "label": "Créer un raccourci dans...",
                "submenu": {
                }
            },
            "duplicate":{
                "icon": "fa fa-copy",
                "label": "Dupliquer",
                "action": function(){
                    idPage = $('#'+node.id).find('.page').attr('id');
                    window.location.href = baseURL+Routing.generate('easyadmin', { action: 'dupliquer', entity: 'Page_Active', id: idPage });
                }
            },
            "delete":{
                "icon": "fa fa-trash-alt",
                "label": "Mettre à la corbeille",
                "action": function(){
                    idPage = $('#'+node.id).find('.page').attr('id');
                    window.location.href = baseURL+Routing.generate('easyadmin', { action: 'corbeille', entity: 'Page_Active', id: idPage });
                }
            }
        };

        //Ajout des sous-menus de "Créer un raccourci dans..."
        $('div[id^="menu"]:not("#menu-0")').each(function(){
            id = $(this).attr('id');

            idMenu = $('#'+node.id).closest('.jstree-container-ul').parent('div').attr('id');
            if(idMenu !== id){
                nom = $('#'+id+' > ul > li > a').text();
            }else{
                nom = "Ce menu";
            }

            items.alias.submenu[id] = {
                "icon": "fas fa-folder",
                "label": nom,
                "menu": id.substr(5),
                "menuComplet": id,
                "action": function(sousMenu){//sousMenu = objet "copier dans menu X"
                    idMenu = sousMenu.item.menu;
                    idMenuComplet = sousMenu.item.menuComplet;

                    idAncienMenuComplet = $('#'+node.id).parents('div').attr('id');

                    idPage = $('#'+node.id).find('.page').attr('id');

                    $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-sync fa-spin'></i>");

                    idAnciendMenuPage = $('#'+node.id).find('.menuPage').attr('id');

                    $.ajax({
                        url: baseURL+Routing.generate('creerAlias'),
                        method: "post",
                        data: {idMenu : idMenu, idPage : idPage, idAncienMenuComplet : idAncienMenuComplet, idAnciendMenuPage : idAnciendMenuPage }
                    })
                        .done(function(data){
                            $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-check'></i>").delay(600).fadeOut();

                            nodeParent = $('#'+idMenuComplet).jstree("get_node", $('#'+idMenuComplet+' > ul > li'));

                            node.children = [];

                            nouveauNode = $('#'+idMenuComplet).jstree("copy_node", node, nodeParent, 'first', function(node, parent, position){
                                $('#'+node.id).find('.menuPage').attr('id', data);
                            });

                            if(idAncienMenuComplet = 'menu-0'){
                                $('#'+idAncienMenuComplet).jstree().delete_node([node.id]);
                            }
                        })
                        .fail(function(){
                            $('#loader-arbo.'+idMenuComplet).html("<i class='fas fa-times'></i>").delay(600).fadeOut();
                        });
                }
            };
        });

        //On enlève les options inutiles pour les menus
        if (node.type === 'root' || node.type === 'orphan'){
            delete items.delete;
            delete items.duplicate;
            // delete items.remove;
            delete items.alias;
            delete items.see;
            delete items.home;
        }

        if($('#'+node.id).closest('div').attr('id') === 'menu-0'){
            delete items.alias;
            // delete items.remove;
            if(node.type !== 'root' && node.type !== 'orphan'){
                delete items.create;
            }else{
                delete items.edit;
            }
        }

        return items;
    }

    //Initialisation JSTree
    options = {
        "plugins" : [
            "dnd", "types", "contextmenu", "state"
        ],
        "contextmenu":{
            "items": menuContextuel
        },
        "types" : {
            "#" : {
                "valid_children" : ["root"]
            },
            "root" : {
                "icon" : "fas fa-folder"
            },
            "default" : {
                "icon" : "fas fa-file"
            },
            "orphan" : {
                "icon" : "fas fa-folder",
                "max_depth" : 1
            },
            "home" : {
                "icon" : "fas fa-home"
            }
        },
        "core" : {
            "animation" : 0,
            "html_titles" : true,
            "check_callback" : true,
            "themes" : { "stripes" : false }
        },
        "dnd": {
            "is_draggable" : function(nodes, event){
                return(nodes[0].type !== 'root' && nodes[0].type !== 'orphan');
            },
            "touch" : "selected"
        }
    };

    // $('.sidebar-menus div[id^="menu"]').jstree(options);//Menus
    $('.sidebar-menus div[id^="menu"]').each(function(){//Initialisation
        options.state = {
            "key" : $(this).attr('id'),
            filter : function (state) {
                delete state.core.selected;
                return state;
            }
        };
        $(this).jstree(options);
    });

    enregistrementMenu = function(e, data){
        $(this).jstree('open_all', data.node.parent);
        idMenuComplet = $('#'+data.node.id).closest('div').attr('id');
        $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-sync fa-spin'></i>");
        arbo = [];
        $(this).find('.jstree-container-ul li').find('li').each(function(){
            position = $(this).index();

            idLi = $(this).attr('id');
            idMenu = $('#'+idLi).parents('div[id^="menu"]').attr('id').substr(5);
            idMenuPage = $('#'+idLi).find('.menuPage').attr('id');
            idPage = $('#'+idLi).find('.page').attr('id');

            if($(this).parent('ul').parent('li').parent('ul').hasClass('jstree-container-ul') || idMenu === '0'){
                idParent = null;
            }else{
                idLiParent = $(this).parent('ul').parent('li').attr('id');
                idParent = $('#'+idLiParent).find('.menuPage').attr('id');
            }
            menuPage = [idMenuPage, idPage, position, idParent, idMenu];

            // console.log(menuPage);

            arbo.push(menuPage);
        });
        $.ajax({
            url: baseURL+Routing.generate('enregistrerMenu'),
            method: "post",
            data: {arbo: arbo}
        })
            .done(function(){
                $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-check'></i>").delay(600).fadeOut();

                if(e.type === 'create_node'){
                    idPage = $('#'+data.node.id).find('.page').attr('id');
                    url = Routing.generate('easyadmin', { 'action' : 'edit', 'entity' : 'Page_Active', 'id' : idPage });
                    window.location.href = url;
                }
            })
            .fail(function(){
                $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-times'></i>").delay(600).fadeOut();
            });
    };

    $('.sidebar-menus div[id^="menu"]').on('select_node.jstree', function (e, data) {
        setTimeout(function() {
            data.instance.show_contextmenu(data.node)
        }, 100);
    });

    $('.sidebar-menus div[id^="menu"]').on('move_node.jstree copy_node.jstree create_node.jstree', enregistrementMenu);

    $('.sidebar-menus div[id^="menu"]').on('create_node.jstree', function(e, data){
        idMenuComplet = $('#'+data.node.id).parents('div').attr('id');
        if(!idMenuComplet === 'menu-0'){
            enregistrementMenu();
        }
    });

    $('.sidebar-menus div[id^="menu"]').on('ready.jstree', function(){
        // $(this).jstree().open_all();
//            context_hide();
    });

    //Pop-up ajout de page
        //Fermeture
    $('body').on('click', '#popup-ajoutPage, .popup-ajoutPage-close', function(){
        $('#popup-ajoutPage').remove();
    });

    $('body').on('click', '#popup-ajoutPage > div', function(e){
        e.stopPropagation();
    });

    function str2url(str,encoding,ucfirst)
    {
        str = str.toUpperCase();
        str = str.toLowerCase();
        str = str.replace(/[\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g,'a');
        str = str.replace(/[\u00E7]/g,'c');
        str = str.replace(/[\u00E8\u00E9\u00EA\u00EB]/g,'e');
        str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g,'i');
        str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8]/g,'o');
        str = str.replace(/[\u00F9\u00FA\u00FB\u00FC]/g,'u');
        str = str.replace(/[\u00FD\u00FF]/g,'y');
        str = str.replace(/[\u00F1]/g,'n');
        str = str.replace(/[\u0153]/g,'oe');
        str = str.replace(/[\u00E6]/g,'ae');
        str = str.replace(/[\u00DF]/g,'ss');
        str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
        str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
        str = str.replace(/[ ]/g,'-');
        if (ucfirst === 1)
        {
            c = str.charAt(0);
            str = c.toUpperCase()+str.slice(1);
        }
        return str;
    }

        //Soumission formulaire
    $('body').on('submit', '#popup-ajoutPage-formulaire', function(e){
        e.preventDefault();

        //Vérif champs
        $(this).find('input[type="text"]').each(function(){
            if(typeof(titre) === 'undefined'){
                titre = $('#ajoutPage-titre').val();
            }
           if($(this).val() === ''){
                $(this).val(titre);
           }
            $('#popup-ajoutPage').hide();
           delete titre;
        });

        //Submit
        idNode = $('#ajoutPage-idNode').val();
        idMenuComplet = $('#ajoutPage-idMenuComplet').val();
        titreMenu = $('#ajoutPage-titreMenu').val();
        $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-sync fa-spin'></i>");

        donneesFormulaire = $(this).serializeArray();
         $.ajax({
             url: baseURL+Routing.generate('ajouterPageEnfant'),
             method: "post",
             data: {donneesFormulaire: donneesFormulaire}
         })
         .done(function(data){
             $('#popup-ajoutPage').remove();
             $('#loader-arbo.'+idMenuComplet).fadeIn().html("<i class='fas fa-check'></i>").delay(600).fadeOut();

             idPage = data.substring(0, data.indexOf('*'));
             idMenuPage = data.substring(data.indexOf('*')+1);

             node = $('#'+idMenuComplet).jstree("get_node", idNode);

             nouveauNode = $('#'+idMenuComplet).jstree("create_node", node, titreMenu+'<span class="menuPage" id="'+idMenuPage+'"></span><span class="page" id="'+idPage+'"></span>', 'first', false, false);

             $('#'+idNode).jstree("open_all");
         })
         .fail(function(){
            $('#loader-arbo.'+idMenuComplet).html("<i class='fas fa-times'></i>").delay(600).fadeOut();
         });
    })
});