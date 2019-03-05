<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/12/2017
 * Time: 15:00
 */

namespace App\Blocs\Menu;

use App\Entity\Langue;
use App\Service\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class MenuTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig, RequestStack $requestStack, Page $spage)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->request = $requestStack->getCurrentRequest();
        $this->spage = $spage;
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

        $page = $this->spage->getPageActive();

        return $this->twig->render('front/menu/menus.html.twig', array('menus' => $menus, 'page' => $page));
    }
}