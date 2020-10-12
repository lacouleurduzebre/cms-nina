<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\PlanDuSite;


use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Bloc;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment;

class PlanDuSiteTwig extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('planDuSite', array($this, 'planDuSite')),
        );
    }

    public function planDuSite()
    {
        $repoMenu = $this->doctrine->getRepository(Menu::class);
        $menus = $repoMenu->findAll();

        return $menus;
    }
}