<?php

include(__DIR__.'/../vendor/symfony/dotenv/Dotenv.php');

$timestamp = time();
$date = date('d/m/Y H:i', $timestamp);

$env = new Symfony\Component\Dotenv\Dotenv();
$env->load(__DIR__.'/../.env');
$mysqlUserName      = getenv('USERDB');
$mysqlPassword      = getenv('PASSWORD');
$mysqlHostName      = getenv('HOST');
$DbName             = getenv('DATABASE');
$prefixe            = getenv('PREFIXE');
$mysqldump=exec('which mysqldump');
$mysql=exec('which mysql');

$command = "$mysql -h $mysqlHostName -u $mysqlUserName --password=$mysqlPassword $DbName -N -e 'show tables like \"$prefixe\_%\"' | xargs $mysqldump -h $mysqlHostName -u $mysqlUserName --password=$mysqlPassword $DbName > ".__DIR__."/../sauvegardes/bdd/dump$timestamp.sql";

exec($command);

exec('zip '.__DIR__.'/../sauvegardes/bdd/dump'.$timestamp.'.zip '.__DIR__.'/../sauvegardes/bdd/dump'.$timestamp.'.sql');

unlink(__DIR__.'/../sauvegardes/bdd/dump'.$timestamp.'.sql');