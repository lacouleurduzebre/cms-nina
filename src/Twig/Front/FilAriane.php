<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 18/07/2018
 * Time: 11:35
 */

namespace App\Twig\Front;


use App\Entity\MenuPage;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class FilAriane extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ariane', array($this, 'ariane'), array('is_safe' => ['html'])),
        );
    }

    public function ariane($page){
        $emMenu = $this->doctrine->getRepository(\App\Entity\Menu::class);
        $menuPrincipal = $emMenu->findOneBy(array('defaut' => true));

        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPage = $emMenuPage->findOneBy(array('menu' => $menuPrincipal, 'page' => $page));

        if($menuPage){
            $pageDansMenuPrincipal = true;
            $ariane = [];

            $page = $menuPage->getPage();
            $ariane[] = $page;

            $pageParent = $menuPage->getPageParent();
            while($pageParent){
                $ariane[] = $pageParent;
                $menuPageParent = $emMenuPage->findOneBy(array('menu' => $menuPrincipal, 'page' => $pageParent));
                $pageParent = $menuPageParent->getPageParent();
            }
            $ariane = array_reverse($ariane);
        }else{
            $pageDansMenuPrincipal = false;
            $ariane = false;
        }

        return $this->twig->render('front/filAriane.html.twig', array('pageDansMenuPrincipal' => $pageDansMenuPrincipal, 'ariane' => $ariane));
    }
}