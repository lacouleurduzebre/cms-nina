<?php
mkdir('sauvegardes');
mkdir('sauvegardes/mediatheque');
mkdir('sauvegardes/bdd');
mkdir('public/themes_thumbs');
mkdir('public/uploads');
mkdir('translations');
mkdir('themes/nina/translations');

if(!file_exists('public/assets')){
    symlink('assets', 'public/assets');
}
if(!file_exists('public/theme')){
    symlink('themes/nina/assets', 'public/theme');
}

if(!file_exists('config/packages/doctrine_migrations.yaml')){
    copy('config/defaut/defaut_doctrine_migrations.yaml', 'config/packages/doctrine_migrations.yaml');
}
if(!file_exists('src/Service/NamingStrategy.php')){
    copy('config/defaut/defaut_NamingStrategy.php', 'src/Service/NamingStrategy.php');
}
if(!file_exists('config/services.yaml')){
    copy('config/defaut/defaut_services.yaml', 'config/services.yaml');
}