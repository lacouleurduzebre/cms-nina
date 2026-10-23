<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2020-03-12
 * Time: 11:23
 */

namespace App\Command;


use App\Controller\Back\SauvegardeController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'nina:sauvegarde:mediatheque')]
class SauvegardeMediathequeCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Sauvegarde la médiathèque dans un fichier zip')
            ->setHelp('Créé un fichier zip du dossier "uploads" dans le dossier sauvegardes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $timestamp = time();
        SauvegardeController::zip(__DIR__.'/../../public/uploads', __DIR__.'/../../sauvegardes/mediatheque/mediatheque'.$timestamp.'.zip');

        return 0;
    }
}