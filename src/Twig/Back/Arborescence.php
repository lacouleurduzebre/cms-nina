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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Arborescence extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack, Environment $twig)
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
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $emMenu = $this->doctrine->getRepository(Menu::class);

        //Langue
        if(isset($_COOKIE['langueArbo'])){
            $langueArbo = $_COOKIE['langueArbo'];
            $langue = $repoLangue->find($langueArbo);
        }else{
            $langue = $repoLangue->findOneBy(array('defaut' => 1));
        }

        //Menus
        $menus = $emMenu->findBy(['langue'=>$langue], ['priorite' => 'ASC']);

        return $this->twig->render('back/menu/arborescence.html.twig', array('menus' => $menus, 'langueArbo' => $langue));
    }

    public function pagesOrphelines()
    {
        $emLangue = $this->doctrine->getRepository(Langue::class);

        if(isset($_COOKIE['langueArbo'])){
            $langueArbo = $_COOKIE['langueArbo'];
            $langue = $emLangue->find($langueArbo);
        }else{
            $langue = $emLangue->findOneBy(array('defaut' => 1));
        }

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

    public function sousMenu($menuPage, $office)
    {
        $repoLangue = $this->doctrine->getRepository(Langue::class);

        $repoMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPages = $repoMenuPage->findBy(array('parent' => $menuPage, 'menu' => $menuPage->getMenu()), array('position' => 'ASC'));

        if ($menuPages){
            if($office == 'back'){
                if(isset($_COOKIE['langueArbo'])){
                    $langueArbo = $_COOKIE['langueArbo'];
                    $langue = $repoLangue->find($langueArbo);
                }else{
                    $langue = $repoLangue->findOneBy(array('defaut' => 1));
                }
                return $this->twig->render('back/menu/sousMenu.html.twig', array('menuPages' => $menuPages, 'langueArbo' => $langue));
            }else{
                return $this->twig->render('front/menu/sousMenu.html.twig', array('menuPages' => $menuPages));
            }
        }else{
            return false;
        }
    }

    public function isParent($menuPage)
    {
        $repoMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPages = $repoMenuPage->findBy(array('parent' => $menuPage, 'menu' => $menuPage->getMenu()));

        if ($menuPages){
            return true;
        }else{
            return false;
        }
    }

    public function pageAccueil()
    {
        $repoLangue = $this->doctrine->getRepository(Langue::class);

        if(isset($_COOKIE['langueArbo'])){
            $langueArbo = $_COOKIE['langueArbo'];
            $langue = $repoLangue->find($langueArbo);
        }else{
            $langue = $repoLangue->findOneBy(array('defaut' => 1));
        }

        if($langue->getPageAccueil() !== null){
            $pageAccueil = $langue->getPageAccueil();
        }else{
            $pageAccueil = null;
        }

        return $this->twig->render('back/menu/pageAccueil.html.twig', array('pageAccueil' => $pageAccueil, 'langueArbo' => $langue));
    }
}