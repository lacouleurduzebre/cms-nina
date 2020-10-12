<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2020-03-12
 * Time: 11:23
 */

namespace App\Command;


use App\Controller\Back\SauvegardeController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;

class SauvegardeBDDCommand extends Command
{
    protected static $defaultName = 'nina:sauvegarde:bdd';

    protected function configure()
    {
        $this->setDescription('Sauvegarde la bdd dans un fichier zip')
            ->setHelp('Créé un fichier zip de l\'export de la bdd dans le dossier sauvegardes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timestamp = time();

        $env = new Dotenv();
        $env->load(__DIR__.'/../../.env');
        $mysqlUserName      = getenv('USERDB');
        $mysqlPassword      = getenv('PASSWORD');
        $mysqlHostName      = getenv('HOST');
        $DbName             = getenv('DATABASE');
        $prefixe            = getenv('PREFIXE');
        $mysqldump=exec('which mysqldump');
        $mysql=exec('which mysql');
        $dossier = __DIR__;

        $command = "$mysql -h $mysqlHostName -u $mysqlUserName --password=$mysqlPassword $DbName -N -e 'show tables like \"$prefixe\_%\"' | xargs $mysqldump -h $mysqlHostName -u $mysqlUserName --password=$mysqlPassword $DbName > $dossier/../../sauvegardes/bdd/dump$timestamp.sql";

        exec($command);

        SauvegardeController::zip(__DIR__.'/../../sauvegardes/bdd/dump'.$timestamp.'.sql', __DIR__.'/../../sauvegardes/bdd/dump'.$timestamp.'.zip');

        unlink(__DIR__.'/../../sauvegardes/bdd/dump'.$timestamp.'.sql');

        return 0;
    }
}