<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 18/07/2018
 * Time: 11:35
 */

namespace App\Twig\Front;


use App\Entity\MenuPage;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class BlocAnnexe extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getBlocAnnexe', array($this, 'getBlocAnnexe')),
        );
    }

    public function getBlocAnnexe($page, $type){
        $repoBlocAnnexe = $this->doctrine->getRepository(\App\Entity\BlocAnnexe::class);
        $blocAnnexe = $repoBlocAnnexe->findOneBy(array('page' => $page, 'type' => $type));

        if($blocAnnexe){
            return $this->twig->render('BlocsAnnexes/'.$type.'/'.$type.'.html.twig', array('bloc' => $blocAnnexe));
        }

        return false;
    }
}