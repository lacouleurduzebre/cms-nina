<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 18/07/2018
 * Time: 11:35
 */

namespace App\Twig\Front;


use App\Entity\MenuPage;
use App\Entity\Page;
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

    public function ariane($idPage){
        $emMenu = $this->doctrine->getRepository(\App\Entity\Menu::class);
        $menuPrincipal = $emMenu->findOneBy(array('defaut' => true));

        $emPage = $this->doctrine->getRepository(Page::class);
        $page = $emPage->find($idPage);

        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPage = $emMenuPage->findOneBy(array('menu' => $menuPrincipal, 'page' => $page));

        if($menuPage){
            $pageDansMenuPrincipal = true;
            $ariane = [];

            $ariane[] = $page;

            $menuPageParent = $menuPage->getParent();
            while($menuPageParent){
                $ariane[] = $menuPageParent->getPage();
                $menuPageParent = $menuPageParent->getParent();
            }
            $ariane = array_reverse($ariane);
        }else{
            $pageDansMenuPrincipal = false;
            $ariane = false;
        }

        return $this->twig->render('front/filAriane.html.twig', array('pageDansMenuPrincipal' => $pageDansMenuPrincipal, 'ariane' => $ariane));
    }
}