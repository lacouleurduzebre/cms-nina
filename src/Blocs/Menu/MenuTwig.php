<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/12/2017
 * Time: 15:00
 */

namespace App\Blocs\Menu;

use Psr\SimpleCache\CacheInterface;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment;

class MenuTwig extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, Environment $twig, CacheInterface $cache)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->cache = $cache;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('menu', array($this, 'menu'), array('is_safe' => ['html'])),
        );
    }

    public function menu($id){
        if($_ENV['APP_ENV'] == 'prod'){
            $tpl = $this->cache->get('menu_' . $id);
        }

        if(!isset($tpl)){
            $emMenu = $this->doctrine->getRepository(\App\Entity\Menu::class);

            $menus = [];
            $menus[] = $emMenu->find($id);

            $tpl = $this->twig->render('front/menu/menus.html.twig', array('menus' => $menus));

            if($_ENV['APP_ENV'] == 'prod'){
                $this->cache->set('menu_' . $id, $tpl);
            }
        }

        return $tpl;
    }
}