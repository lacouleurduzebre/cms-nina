<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 13:14
 */

namespace App\Twig\Back;


use App\Entity\Bloc;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BlocsPartages extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('blocsPartages', array($this, 'blocsPartages')),
        );
    }

    public function blocsPartages($idBloc)
    {
        $repoBloc = $this->doctrine->getRepository(Bloc::class);
        $blocsPartages = $repoBloc->findBy(['type' => 'BlocPartage']);

        $blocs = [];

        foreach($blocsPartages as $blocPartage){
            if($blocPartage->getContenu()['blocPartage'] == $idBloc){
                $blocs[] = $blocPartage;
            }
        }

        return $blocs;
    }
}