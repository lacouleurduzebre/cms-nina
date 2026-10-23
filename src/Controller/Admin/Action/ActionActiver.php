<?php

namespace App\Controller\Admin\Action;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

/**
 * Action de CRUD : Activer / désactiver une page
 */
trait ActionActiver
{
    #[AdminRoute(path: '/{entityId}/activer', name: 'activer', options: ['methods' => ['GET']])]
    public function activer(AdminContext $context)
    {
        $page = $context->getEntity()->getInstance();

        $activation = $page->getActive();
        if($activation){
            $this->addFlash( 'success',
                'La page a été désactivée.'
            );
        }else{
            $this->addFlash( 'success',
                'La page a été activée.'
            );
        }
        $page->setActive(!$activation);
        $this->em()->persist($page);
        $this->em()->flush();

        return $this->redirect($this->adminUrl->crud('Page_Active', Action::EDIT, $page->getId()));
    }
}
