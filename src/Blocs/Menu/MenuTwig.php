<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/12/2017
 * Time: 15:00
 */

namespace App\Blocs\Menu;

use App\Entity\Langue;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class MenuTwig extends \Twig_Extension
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

    public function menu($id){
        $emMenu = $this->doctrine->getRepository(\App\Entity\Menu::class);

        $menus = [];
        $menus[] = $emMenu->find($id);

        return $this->twig->render('front/menu/menus.html.twig', array('menus' => $menus));
    }
}