<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 26/02/2019
 * Time: 14:42
 */

namespace App\Controller\Back\Admin;


use App\Controller\Back\AdminController;
use App\Entity\Categorie;
use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PageAdminController
 * @package App\Controller\Back\Admin
 * @Route("/admin")
 */
class PageAdminController extends AdminController
{
    //Afficher toutes les pages d'une catégorie avec ?categorie=X
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        $qb = $em->createQueryBuilder()
            ->select('entity')
            ->from($this->entity['class'], 'entity');

        $request = $this->get('request_stack')->getCurrentRequest();
        $categorie = $request->get('categorie');
        if ($categorie) {
            $qb->andWhere(':categorie MEMBER OF entity.categories');
            $qb->setParameters(array('categorie' => $categorie));
        }

        if (!empty($dqlFilter)) {
            $qb->andWhere($dqlFilter);
        }

        if (null !== $sortField) {
            $qb->orderBy('entity.'.$sortField, $sortDirection ?: 'DESC');
        }

        return $qb;
    }

    protected function persistPage_ActiveEntity($entity)//new
    {
        $this::persistEntity($entity);

        $this->setTraductions($entity);

        $this->getDoctrine()->getManager()->flush();
    }

    protected function updatePage_ActiveEntity($entity)//edit
    {
        $this->setTraductions($entity);

        $this::updateEntity($entity);
    }

    //Nouvelle page : checkboxes affichage commentaires / date publi / auteur en fonction de la config
    protected function createPage_ActiveEntityFormBuilder($entity, $view){
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        if($view == "new") {
            $config = $this->getDoctrine()->getRepository(Configuration::class)->find(1);
            $page = $formBuilder->getData();

            $page->setAffichageCommentaires($config->getAffichageCommentaires());
            $page->setAffichageDatePublication($config->getAffichageDatePublication());
            $page->setAffichageAuteur($config->getAffichageAuteur());
        }

        return $formBuilder;
    }

    //Ajout de la liste des blocs dans $parameters
    protected function newPage_ActiveAction(){
        return $this->newAvecListeBlocs();
    }

    protected function editPage_ActiveAction(){
        return $this->editAvecListeBlocs();
    }

    //Traductions de page : ajout de la langue et de la page originale dans les données du formulaire
    protected function createNewPage_ActiveEntity()
    {
        if($this->request->get('pageOriginale')){
            $pageOriginale = $this->getDoctrine()->getRepository(Page::class)->find($this->request->get('pageOriginale'));

            $nvPage = new Page();

            //Contenus
            $nvPage->setTitre($pageOriginale->getTitre());
            $nvPage->setTitreMenu($pageOriginale->getTitreMenu());

            $blocs = $pageOriginale->getBLocs();
            foreach($blocs as $bloc){
                $nvBloc = clone $bloc;
                $nvPage->addBloc($nvBloc);
            }

            //SEO
            $nvSEO = clone $pageOriginale->getSEO();
            $nvPage->setSEO($nvSEO);

            //Affichage
            $blocsAnnexes = $pageOriginale->getBLocsAnnexes();
            foreach($blocsAnnexes as $blocAnnexe){
                $nvBlocAnnexe = clone $blocAnnexe;
                $nvPage->addBlocsAnnex($nvBlocAnnexe);
            }

            //Catégories
            $categories = $pageOriginale->getCategories();
            foreach($categories as $categorie){
                $nvPage->addCategory($categorie);
            }

            $langue = $this->getDoctrine()->getRepository(Langue::class)->find($this->request->get('langue'));
            $nvPage->setLangue($langue);

            $trads = $nvPage->getTraductions();
            $trads[$pageOriginale->getLangue()->getId()] = $this->request->get('pageOriginale');
            $nvPage->setTraductions($trads);

            return $nvPage;
        }else{
            return parent::createNewEntity();
        }
    }

    protected function renderPage_ActiveTemplate($actionName, $templatePath, $parameters){
        $request = $this->get('request_stack')->getCurrentRequest();
        $idCategorie = $request->get('categorie');

        if($actionName == 'list' && $idCategorie){
            $repoCategorie = $this->getDoctrine()->getRepository(Categorie::class);
            $categorie = $repoCategorie->find($idCategorie);
            $parameters['filtreCategorie'] = $categorie;
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    protected function setTraductions($entity){
        $em = $this->getDoctrine()->getManager();
        $repoPage = $this->getDoctrine()->getRepository(Page::class);

        $langue = $entity->getLangue()->getId();
        $id = $entity->getId();
        $traductions = $entity->getTraductions();

        foreach($traductions as $traduction){
            if($traduction){
                $page = $repoPage->find($traduction);
                $trads = $page->getTraductions();
                $trads[$langue] = $id;
                $page->setTraductions($trads);
                $em->persist($page);
            }
        }
    }
}