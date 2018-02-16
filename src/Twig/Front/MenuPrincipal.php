<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 11/09/2017
 * Time: 13:50
 */

namespace App\Twig\Front;


use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class MenuPrincipal extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, RequestStack $requestStack, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->request = $requestStack->getCurrentRequest();
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('menuFront', array($this, 'menuFront')),
        );
    }

    public function menuFront()
    {
        $emPage = $this->doctrine->getRepository(Page::class);
        $emLangue = $this->doctrine->getRepository(Langue::class);

        $locale = $this->request->getLocale();
        $langueActive = $emLangue->findBy(array('abreviation' => $locale));

        $pages = $emPage->pagesPubliees($langueActive, true);

        return $this->twig->render('front/menu/menu.html.twig', array('pages' => $pages));
    }
}