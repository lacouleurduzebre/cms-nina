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
use App\Entity\Bloc;
use App\Service\Page;
use App\Service\Pagination;
use Doctrine\Persistence\ManagerRegistry;

class RubriqueTwig extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listePagesEnfants', array($this, 'listePagesEnfants')),
        );
    }

    public function listePagesEnfants($pageParent, $parametres)
    {
        $repoMenu = $this->doctrine->getRepository(Menu::class);
        $menuPrincipal = $repoMenu->findOneBy(array('defaut' => true, 'langue' => $pageParent->getLangue()));

        $repoMenuPage = $this->doctrine->getRepository(MenuPage::class);

        $menuPage = $repoMenuPage->findOneBy(array('page' => $pageParent, 'menu' => $menuPrincipal));

        $pages = [];
        if($menuPage){
            $menusPagesEnfants = $repoMenuPage->findBy(array('parent' => $menuPage), array('position' => 'ASC'));
            foreach($menusPagesEnfants as $menuPageEnfant){
                $pageEnfant = $menuPageEnfant->getPage();

                if(Page::isPublie($pageEnfant)){
                    $pages[] = $pageEnfant;
                }
            }
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->pagination->getPagination($pages, $parametres, $page);
    }
}