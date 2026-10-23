<?php

namespace App\Controller\Admin\Action;

use App\Entity\MenuPage;
use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * Action de CRUD : Mettre à la corbeille / restaurer (pages, commentaires)
 */
trait ActionCorbeille
{
    #[AdminRoute(path: '/{entityId}/corbeille', name: 'corbeille', options: ['methods' => ['GET']])]
    public function corbeille(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();
        $em = $this->em();

        if($entity->getCorbeille()){
            $entity->setCorbeille(false);

            if($entity instanceof Page){
                $menuPage = new MenuPage();
                $menuPage->setPage($entity)->setPosition(0);
                $em->persist($menuPage);
                $em->flush();
            }

            //Message flash
            $this->addFlash( 'success',
                'L\'élément a été restauré.'
            );
        }else{
            $entity->setCorbeille(true);

            if($entity instanceof Page){
                $repoMenuPage = $em->getRepository(MenuPage::class);
                $menuPages = $repoMenuPage->findBy(array('page' => $entity));

                foreach($menuPages as $menuPage){
                    $em->remove($menuPage);

                    //Si la page était parent d'une autre, on remonte ses enfants à la racine du menu
                    $menuPagesOrphelines = $repoMenuPage->findBy(array('parent' => $menuPage));
                    foreach($menuPagesOrphelines as $menuPage){
                        $menuPage->setParent(null);
                    }

                    //Si la page était la page d'accueil, la langue de la page n'a plus de page d'accueil
                    $langue = $menuPage->getPage()->getLangue();
                    if($langue->getPageAccueil() == $menuPage->getPage()){
                        $langue->setPageAccueil(null);
                    }
                }

                $em->flush();
            }

            //Message flash
            $url = $this->adminUrl->crud(static::ENTITE, 'corbeille', $entity->getId());
            $this->addFlash( 'success',
                sprintf('L\'élément a été mis à la corbeille. <a href="%s">Annuler</a>', $url)
            );
        }
        $em->flush();

        if(!$context->getRequest()->isXmlHttpRequest()){
            return $this->redirect($this->adminUrl->crud(static::ENTITE));
        }

        return new Response('ok');
    }
}
