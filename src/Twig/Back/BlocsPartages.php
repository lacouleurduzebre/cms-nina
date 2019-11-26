<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 13:14
 */

namespace App\Twig\Back;


use App\Entity\Bloc;
use App\Entity\BlocPartage;
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
            new \Twig_SimpleFunction('getBlocPartage', array($this, 'getBlocPartage')),
        );
    }

    //Récupérer le bloc partagé correspondant à un bloc
    public function getBlocPartage($idBloc){
        $repoBlocPartage = $this->doctrine->getRepository(BlocPartage::class);
        $blocPartage = $repoBlocPartage->findOneBy(['bloc' => $idBloc]);

        return $blocPartage;
    }

    public function blocsPartages($idBloc = null)
    {
        if($idBloc){//Tous les blocs liés à un bloc partagé
            $repoBloc = $this->doctrine->getRepository(Bloc::class);
            $blocsPartages = $repoBloc->findBy(['type' => 'BlocPartage']);

            $blocs = [];

            foreach($blocsPartages as $blocPartage){
                if($blocPartage->getContenu()['blocPartage'] == $idBloc){
                    $blocs[] = $blocPartage;
                }
            }

            return $blocs;
        }else{//Tous les blocs partagés
            $repoBlocPartage = $this->doctrine->getRepository(BlocPartage::class);
            $blocsPartages = $repoBlocPartage->findAll();

            return $blocsPartages;
        }
    }
}