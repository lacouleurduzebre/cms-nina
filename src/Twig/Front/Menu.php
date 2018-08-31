<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/12/2017
 * Time: 15:00
 */

namespace App\Twig\Front;

use App\Entity\Langue;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Menu extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig, RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('menu', array($this, 'menu'), array('is_safe' => ['html'])),
        );
    }

    public function menu($region = null, $id = null){
        //Langue
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $locale = $this->request->getLocale();
        if($locale){
            $langue = $repoLangue->findBy(array('abreviation'=>$locale));
        }
        if(!$locale || !$langue){
            $langue = $repoLangue->findBy(array('defaut'=>1));
        }

        $emMenu = $this->doctrine->getRepository(\App\Entity\Menu::class);

        if($region){
            $menus = $emMenu->findBy(array('region' => $region, 'langue'=>$langue));
        }else{
            $menus = [];
            $menus[] = $emMenu->find($id);
        }

        return $this->twig->render('front/menu/menus.html.twig', array('menus' => $menus));
    }
}