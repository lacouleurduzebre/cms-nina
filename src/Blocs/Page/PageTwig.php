<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 10:19
 */

namespace App\Blocs\Page;


use App\Entity\Categorie;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class PageTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('blocs', array($this, 'blocs')),
        );
    }

    public function blocs($idPage)
    {
        $repoPage = $this->doctrine->getRepository(Page::class);
        $page = $repoPage->find($idPage);

        return $this->twig->render('front/blocs.html.twig', array('blocs' => $page->getBlocs()));
    }
}