<?php

namespace App\Controller\Admin\Action;

use App\Entity\Categorie;
use App\Entity\TypeCategorie;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

/**
 * Action de CRUD : Voir l'élément sur le site (page, catégorie, type de catégorie)
 */
trait ActionVoir
{
    #[AdminRoute(path: '/{entityId}/voir', name: 'voir', options: ['methods' => ['GET']])]
    public function voir(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();

        if($entity instanceof Categorie){
            $urlCategorie = $entity->getSEO()->getUrl();
            $urlTypeCategorie = $entity->getTypeCategorie()->getSEO()->getUrl();
            return $this->redirectToRoute('voirCategorie', array('urlTypeCategorie' => $urlTypeCategorie, 'urlCategorie' => $urlCategorie));
        }
        else if($entity instanceof TypeCategorie){
            $urlTypeCategorie = $entity->getSEO()->getUrl();
            return $this->redirectToRoute('voirTypeCategorie', array('urlTypeCategorie' => $urlTypeCategorie));
        }else{
            $url = $entity->getSeo()->getUrl();
            $locale = $entity->getLangue()->getAbreviation();
            return $this->redirectToRoute('voirPageLocale', array('_locale' => $locale, 'url' => $url));
        }
    }
}
