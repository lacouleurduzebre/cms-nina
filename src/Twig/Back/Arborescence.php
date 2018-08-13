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
        );
    }

    public function menusBack()
    {
        //Langue
        $locale = $this->request->getLocale();
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $langue = $repoLangue->findBy(array('abreviation' => $locale));

        //Menus
        $emMenu = $this->doctrine->getRepository(Menu::class);
        $menus = $emMenu->findBy(array('langue'=>$langue));

        return $this->twig->render('back/menu/arborescence.html.twig', array('menus' => $menus));
    }

    public function pagesOrphelines()
    {
        $emLangue = $this->doctrine->getRepository(Langue::class);
        $langue = $emLangue->findOneBy(array('abreviation' => $this->request->getLocale()));

        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);

        $menuPagesOrphelines = $emMenuPage->findBy(array('menu' => null));

        $pagesOrphelines = [];
        foreach($menuPagesOrphelines as $menuPage){
            $page = $menuPage->getPage();

            if($page->getLangue() == $langue){
                $pagesOrphelines[] = $menuPage;
            }
        }

        return $this->twig->render('back/menu/pagesOrphelines.html.twig', array('menuPagesOrphelines' => $pagesOrphelines));
    }
}