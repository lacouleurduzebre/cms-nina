<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 08/08/2018
 * Time: 08:38
 */

namespace App\Twig\Back;


use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PageAccueil extends \Twig_Extension
{
    private $request;
    private $doctrine;
    private $twig;

    public function __construct(RegistryInterface $doctrine, Environment $twig, RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pageAccueil', array($this, 'pageAccueil')),
        );
    }

    public function pageAccueil()
    {
        $locale = $this->request->getLocale();
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $langue = $repoLangue->findOneBy(array('abreviation' => $locale));

        if($langue->getPageAccueil() !== null){
            $pageAccueil = $langue->getPageAccueil();
        }else{
            $pageAccueil = null;
        }

        return $this->twig->render('back/menu/pageAccueil.html.twig', array('pageAccueil' => $pageAccueil));
    }
}