<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 02/07/2018
 * Time: 14:34
 */

namespace App\Twig\Back;


use App\Entity\MenuPage;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class SousMenu extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sousMenu', array($this, 'sousMenu')),
            new \Twig_SimpleFunction('isParent', array($this, 'isParent')),
        );
    }

    public function sousMenu($id, $office)
    {
        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPages = $emMenuPage->findBy(array('pageParent' => $id));

        if ($menuPages){
            return $this->twig->render($office.'/menu/sousMenu.html.twig', array('menuPages' => $menuPages));
        }else{
            return false;
        }
    }

    public function isParent($id)
    {
        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPages = $emMenuPage->findBy(array('pageParent' => $id));

        if ($menuPages){
            return true;
        }else{
            return false;
        }
    }
}