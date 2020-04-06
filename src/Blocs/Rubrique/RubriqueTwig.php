<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\Rubrique;


use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Service\Page;
use App\Service\Pagination;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RubriqueTwig extends AbstractExtension
{
    private $pagination;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('listePagesEnfants', array($this, 'listePagesEnfants')),
        );
    }

    public function listePagesEnfants($pageParent, $parametres)
    {
        $repoMenu = $this->doctrine->getRepository(Menu::class);
        $repoMenuPage = $this->doctrine->getRepository(MenuPage::class);

        $menusPages = $repoMenuPage->findBy(['page' => $pageParent]);

        if(count($menusPages) == 1){//Si la page n'est que dans un menu
            $menuPage = $menusPages[0];
        }else{//Si la page est dans plusieurs menus, on prend le menu par défaut
            $menu = $repoMenu->findOneBy(array('defaut' => true, 'langue' => $pageParent->getLangue()));
            $menuPage = $repoMenuPage->findOneBy(array('page' => $pageParent, 'menu' => $menu));
            if(!$menuPage){//Si la page n'est pas dans le menu par défaut, on prend le premier menuPage
                $menuPage = $menusPages[0];
            }
        }

        $pages = [];
        $menusPagesEnfants = $repoMenuPage->findBy(array('parent' => $menuPage), array('position' => 'ASC'));
        foreach($menusPagesEnfants as $menuPageEnfant){
            $pageEnfant = $menuPageEnfant->getPage();

            if(Page::isPublie($pageEnfant)){
                $pages[] = $pageEnfant;
            }
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->pagination->getPagination($pages, $parametres, $page);
    }
}