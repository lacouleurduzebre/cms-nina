<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 11/09/2017
 * Time: 13:50
 */

namespace App\Twig\Back;


use App\Entity\Langue;
use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Page;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Traduction extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->request = $requestStack->getCurrentRequest();
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getTraduction', array($this, 'getTraduction')),
        );
    }

    public function getTraduction($idPage)
    {
        $repoPage = $this->doctrine->getRepository(Page::class);
        $traduction = $repoPage->find($idPage);

        return array('id' => $idPage, 'titre' => $traduction->getTitre());
    }
}