<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\PlanDuSite;


use App\Entity\Menu;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PlanDuSiteTwig extends AbstractExtension
{
    private $doctrine;
    private $twig;

    public function __construct(ManagerRegistry $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('planDuSite', array($this, 'planDuSite')),
        );
    }

    public function planDuSite()
    {
        $repoMenu = $this->doctrine->getRepository(Menu::class);
        $menus = $repoMenu->findAll();

        return $menus;
    }
}
