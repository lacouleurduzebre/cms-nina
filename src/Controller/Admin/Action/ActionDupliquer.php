<?php

namespace App\Controller\Admin\Action;

use App\Entity\MenuPage;
use App\Entity\SEOPage;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

/**
 * Action de CRUD : Dupliquer une page, avec ses blocs et son SEO
 */
trait ActionDupliquer
{
    #[AdminRoute(path: '/{entityId}/dupliquer', name: 'dupliquer', options: ['methods' => ['GET']])]
    public function dupliquer(AdminContext $context)
    {
        $em = $this->em();
        $pageOriginale = $context->getEntity()->getInstance();

        $nouvellePage = clone $pageOriginale;

        //SEO
        $ancienSEO = $pageOriginale->getSEO();
        $nouveauSEO = clone $ancienSEO;
        $nouvellePage->setSEO($nouveauSEO);
            //Vérif url
        $repoSEO = $em->getRepository(SEOPage::class);
        $url = $nouvellePage->getSEO()->getUrl().'-copie';
        while($repoSEO->findOneBy(array('url' => $url))){
            $url = $url.'-copie';
        }
        if(strlen($url) > 190){//Nouvelle url trop longue -> erreur, on arrête la duplication
            $this->addFlash('-danger', "L'url du nouveau contenu serait trop longue, réduisez la longueur de l'url du contenu que vous souhaitez dupliquer");
            return $this->redirect($context->getRequest()->headers->get('referer') ?? $this->adminUrl->crud(static::ENTITE, Action::EDIT, $pageOriginale->getId()));
        }
        $nouvellePage->getSEO()->setUrl($url);
        $em->persist($nouveauSEO);

        //Menu page
        $menuPage = new MenuPage();
        $menuPage->setPage($nouvellePage)->setPosition(0);
        $em->persist($menuPage);

        //Blocs
        $blocs = $pageOriginale->getBlocs();
        foreach($blocs as $ancienBloc){
            $nouveauBloc = clone $ancienBloc;
            $nouvellePage->addBloc($nouveauBloc);
            $em->persist($nouveauBloc);

            if($nouveauBloc->getType() == 'Section'){
                $this->duplicationBlocsEnfants($nouveauBloc);
            }
        }

        $em->persist($nouvellePage);
        $em->flush();

        return $this->redirect($this->adminUrl->crud('Page_Active', Action::EDIT, $nouvellePage->getId()));
    }
    private function duplicationBlocsEnfants($bloc){
        $blocsEnfants = $bloc->getBlocsEnfants();
        foreach($blocsEnfants as $blocEnfant){
            $nouveauBlocEnfant = clone $blocEnfant;
            $nouveauBlocEnfant->setBlocParent($bloc);
            $this->em()->persist($nouveauBlocEnfant);

            if($nouveauBlocEnfant->getType() == 'Section'){
                $this->duplicationBlocsEnfants($nouveauBlocEnfant);
            }
        }
    }
}
