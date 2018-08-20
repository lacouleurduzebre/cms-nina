<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 11/09/2017
 * Time: 13:50
 */

namespace App\Twig\Back;


use App\Entity\Langue;
use App\Entity\Menu;
use App\Entity\MenuPage;
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
            new \Twig_SimpleFunction('menusBack', array($this, 'menusBack')),
            new \Twig_SimpleFunction('pagesOrphelines', array($this, 'pagesOrphelines')),
            new \Twig_SimpleFunction('sousMenu', array($this, 'sousMenu')),
            new \Twig_SimpleFunction('isParent', array($this, 'isParent')),
            new \Twig_SimpleFunction('pageAccueil', array($this, 'pageAccueil')),
        );
    }

    public function menusBack()
    {
        //Langue
        if(!isset($_COOKIE['langueArbo'])){
            $menus = null;
            $langue = null;
        }else{
            $langueArbo = $_COOKIE['langueArbo'];

            $repoLangue = $this->doctrine->getRepository(Langue::class);
            $langue = $repoLangue->find($langueArbo);

            //Menus
            $emMenu = $this->doctrine->getRepository(Menu::class);
            $menus = $emMenu->findBy(array('langue'=>$langue));
        }

        return $this->twig->render('back/menu/arborescence.html.twig', array('menus' => $menus, 'langueArbo' => $langue));
    }

    public function pagesOrphelines()
    {
        $langueArbo = $_COOKIE['langueArbo'];

        $emLangue = $this->doctrine->getRepository(Langue::class);
        $langue = $emLangue->find($langueArbo);

        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);

        $menuPagesOrphelines = $emMenuPage->findBy(array('menu' => null));

        $pagesOrphelines = [];
        foreach($menuPagesOrphelines as $menuPage){
            $page = $menuPage->getPage();

            if($page->getLangue() == $langue){
                $pagesOrphelines[] = $menuPage;
            }
        }

        return $this->twig->render('back/menu/pagesOrphelines.html.twig', array('menuPagesOrphelines' => $pagesOrphelines, 'langueArbo' => $langue));
    }

    public function sousMenu($id, $office)
    {
        $repoLangue = $this->doctrine->getRepository(Langue::class);

        if($office == 'back'){
            $langue = $_COOKIE['langueArbo'];
            $langueArbo = $repoLangue->find($langue);
        }else{
            $langueArbo = null;
        }

        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPages = $emMenuPage->findBy(array('pageParent' => $id));

        if ($menuPages){
            return $this->twig->render($office.'/menu/sousMenu.html.twig', array('menuPages' => $menuPages, 'langueArbo' => $langueArbo));
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

    public function pageAccueil()
    {
        $langueArbo = $_COOKIE['langueArbo'];
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $langue = $repoLangue->find($langueArbo);

        if($langue->getPageAccueil() !== null){
            $pageAccueil = $langue->getPageAccueil();
        }else{
            $pageAccueil = null;
        }

        return $this->twig->render('back/menu/pageAccueil.html.twig', array('pageAccueil' => $pageAccueil, 'langueArbo' => $langue));
    }
}