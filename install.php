<?php

include('vendor/symfony/filesystem/Filesystem.php');

//Création des dossiers
if(!file_exists('sauvegardes')){
    mkdir('sauvegardes');
}
if(!file_exists('sauvegardes/mediatheque')){
    mkdir('sauvegardes/mediatheque');
}
if(!file_exists('sauvegardes/bdd')){
    mkdir('sauvegardes/bdd');
}
if(!file_exists('public/themes_thumbs')){
    mkdir('public/themes_thumbs');
}
if(!file_exists('public/uploads')){
    mkdir('public/uploads');
}
if(!file_exists('translations')){
    mkdir('translations');
}
if(!file_exists('themes/nina/translations')){
    mkdir('themes/nina/translations');
}

//Symlink des assets du back-office et du thème Nina
$filesystem = new \Symfony\Component\Filesystem\Filesystem();
if(!file_exists('public/assets')){
    $filesystem->symlink(getcwd().'/assets', getcwd().'/public/assets');
}
if(!file_exists('public/theme')){
    $filesystem->symlink(getcwd().'/themes/nina/assets', getcwd().'/public/theme');
}


//Copie des fichiers de conf par défaut
if(!file_exists('config/packages/doctrine_migrations.yaml')){
    copy('config/defaut/defaut_doctrine_migrations.yaml', 'config/packages/doctrine_migrations.yaml');
}
if(!file_exists('config/services.yaml')){
    copy('config/defaut/defaut_services.yaml', 'config/services.yaml');
}
if(!file_exists('src/Blocs/configBlocs.yaml')){
    copy('config/defaut/defaut_configBlocs.yaml', 'src/Blocs/configBlocs.yaml');
}