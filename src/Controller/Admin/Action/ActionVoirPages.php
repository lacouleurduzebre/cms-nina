<?php

namespace App\Controller\Admin\Action;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

/**
 * Action de CRUD : Pages d'une catégorie
 */
trait ActionVoirPages
{
    #[AdminRoute(path: '/{entityId}/pages', name: 'voir_pages', options: ['methods' => ['GET']])]
    public function voirPages(AdminContext $context)
    {
        return $this->redirect($this->adminUrl->crud('Page_Active', Action::INDEX, null, ['categorie' => $context->getEntity()->getPrimaryKeyValue()]));
    }
}
