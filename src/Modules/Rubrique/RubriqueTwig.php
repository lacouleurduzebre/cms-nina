<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Modules\Rubrique;


use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Module;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RubriqueTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listerPagesEnfants', array($this, 'listerPagesEnfants')),
        );
    }

    public function listerPagesEnfants($page)
    {
        $repoMenu = $this->doctrine->getRepository(Menu::class);
        $menuPrincipal = $repoMenu->findOneBy(array('defaut' => true));

        $repoMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menuPage = $repoMenuPage->findBy(array('menu' => $menuPrincipal, 'page' => $page));

        if($menuPage){
            $pages = [];
            $menusPagesEnfants = $repoMenuPage->findBy(array('pageParent' => $page));
            foreach($menusPagesEnfants as $menuPageEnfant){
                $pageEnfant = $menuPageEnfant->getPage();
                $pages[] = $pageEnfant;
            }
        }else{
            $pages = null;
        }

        return array('pages' => $pages);
    }
}