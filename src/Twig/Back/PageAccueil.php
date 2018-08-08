<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 08/08/2018
 * Time: 08:38
 */

namespace App\Twig\Back;


use App\Entity\Configuration;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class PageAccueil extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pageAccueil', array($this, 'pageAccueil')),
        );
    }

    public function pageAccueil($locale)
    {
        $config = $this->doctrine->getRepository(Configuration::class)->find(1);
        $pageAccueil = $config->getPageAccueil();

        return $this->twig->render('back/menu/pageAccueil.html.twig', array('pageAccueil' => $pageAccueil));
    }
}