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
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment;

class FilAriane extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, Environment $twig)
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
        $emPage = $this->doctrine->getRepository(Page::class);
        $page = $emPage->find($idPage);

        $emMenuPage = $this->doctrine->getRepository(MenuPage::class);
        $menusPages = $emMenuPage->findBy(['page' => $page]);

        if($menusPages){
            usort($menusPages, function($a, $b){
                if ($a->getMenu()->getPriorite() == $b->getMenu()->getPriorite()) {
                    return 0;
                }
                return ($a->getMenu()->getPriorite() < $b->getMenu()->getPriorite()) ? -1 : 1;
            });
            $menuPage = $menusPages[0];

            $ariane = [];

            $ariane[] = $page;

            $menuPageParent = $menuPage->getParent();
            while($menuPageParent){
                $ariane[] = $menuPageParent->getPage();
                $menuPageParent = $menuPageParent->getParent();
            }
            $ariane = array_reverse($ariane);
        }else{
            $ariane = false;
        }

        return $this->twig->render('front/filAriane.html.twig', ['ariane' => $ariane]);
    }
}