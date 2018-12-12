<?php
mkdir('sauvegardes');
mkdir('sauvegardes/mediatheque');
mkdir('sauvegardes/bdd');
mkdir('public/uploads');
mkdir('translations');
mkdir('themes/nina/translations');

//symlink('assets', 'public/assets');
//symlink('themes/nina/assets', 'public/theme');

copy('config/defaut/defaut_doctrine_migrations.yaml', 'config/packages/doctrine_migrations.yaml');
copy('config/defaut/defaut_NamingStrategy.php', 'src/Service/NamingStrategy.php');
copy('config/defaut/defaut_services.yaml', 'config/services.yaml');