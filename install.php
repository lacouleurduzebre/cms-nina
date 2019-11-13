<?php

include('vendor/symfony/filesystem/Filesystem.php');

//Création des dossiers
$dossiers = [
    'sauvegardes',
    'sauvegardes/mediatheque',
    'sauvegardes/bdd',
    'public/themes_thumbs',
    'public/uploads',
    'translations',
    'themes/nina/translations',
    'themes/nina/templates',
    'src/Blocs/LEI/cache'
];

foreach($dossiers as $dossier){
    if(!file_exists($dossier)){
        mkdir($dossier);
    }
}

//Symlink des assets du back-office et du thème Nina
$filesystem = new \Symfony\Component\Filesystem\Filesystem();
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
if(!file_exists('src/Blocs/LEI/configLEI.yaml')){
    copy('config/defaut/defaut_configLEI.yaml', 'src/Blocs/LEI/configLEI.yaml');
}