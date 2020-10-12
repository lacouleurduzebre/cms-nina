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

class SauvegardeMediathequeCommand extends Command
{
    protected static $defaultName = 'nina:sauvegarde:mediatheque';

    protected function configure()
    {
        $this->setDescription('Sauvegarde la médiathèque dans un fichier zip')
            ->setHelp('Créé un fichier zip du dossier "uploads" dans le dossier sauvegardes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timestamp = time();
        SauvegardeController::zip(__DIR__.'/../../public/uploads', __DIR__.'/../../sauvegardes/mediatheque/mediatheque'.$timestamp.'.zip');

        return 0;
    }
}