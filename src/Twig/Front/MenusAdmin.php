<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/12/2017
 * Time: 15:00
 */

namespace App\Twig\Front;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class MenusAdmin extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('menu', array($this, 'menu'), array('is_safe' => ['html'])),
        );
    }

    public function menu($region){
        $emMenu = $this->doctrine->getRepository(\App\Entity\Menu::class);
        $menus = $emMenu->findBy(array('region' => $region));

        return $this->twig->render('front/menu/menusAdmin.html.twig', array('menus' => $menus));
    }
}