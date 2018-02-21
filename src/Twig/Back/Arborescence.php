<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 11/09/2017
 * Time: 13:50
 */

namespace App\Twig\Back;


use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Arborescence extends \Twig_Extension
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
            new \Twig_SimpleFunction('menuBack', array($this, 'menuBack')),
        );
    }

    public function menuBack()
    {
        $emPage = $this->doctrine->getRepository(Page::class);
        $emLangue = $this->doctrine->getRepository(Langue::class);

        $locale = $this->request->getLocale();
        $langues = $emLangue->findBy(array('active' => '1'));
        $langueActive = $emLangue->findBy(array('abreviation' => $locale));

        $pages = $emPage->findBy(
            array('pageParent' => null, 'active' => '1', 'corbeille' => '0', 'langue' => $langueActive),
            array('position'=>'asc')
        );

        return $this->twig->render('back/menu/arborescence.html.twig', compact('pages','langues'));
    }
}