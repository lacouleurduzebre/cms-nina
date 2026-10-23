<?php

namespace App\Twig\Back;

use App\Service\AdminUrl;
use App\Service\MenuAdmin;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Admin extends AbstractExtension
{
    private $adminUrl;
    private $menuAdmin;

    public function __construct(AdminUrl $adminUrl, MenuAdmin $menuAdmin)
    {
        $this->adminUrl = $adminUrl;
        $this->menuAdmin = $menuAdmin;
    }

    public function getFunctions(): array
    {
        return array(
            //admin_url('Page_Active', 'edit', page.id) : URL d'un CRUD à partir du nom d'entité d'EasyAdmin 2
            new TwigFunction('admin_url', array($this->adminUrl, 'crud')),
            //admin_route('droits') : page du back-office hors CRUD, affichée dans le layout d'EasyAdmin
            new TwigFunction('admin_route', array($this->adminUrl, 'route')),
            new TwigFunction('admin_tableau_de_bord', array($this->adminUrl, 'tableauDeBord')),
            new TwigFunction('menuAdmin', array($this->menuAdmin, 'getMenu')),
        );
    }
}
