<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 19/02/2018
 * Time: 10:28
 */

namespace App\Controller\Back;

use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Entity\Langue;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\TypeCategorie;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class AdminController extends BaseAdminController
{
    protected function updateEntity($entity)//edit
    {
        parent::updateEntity($entity);

        $this->addFlash('enregistrement', "\"".$entity."\" : enregistrement terminé");
    }

    protected function persistEntity($entity)//new
    {
        parent::persistEntity($entity);

        $this->addFlash('enregistrement', "\"".$entity."\" : enregistrement terminé");
    }

    protected function persistPage_ActiveEntity($entity)
    {
        foreach($entity->getBlocs() as $bloc){
            if(!$bloc->getType()){
                $entity->removeBloc($bloc);
            }
        }

        $this::persistEntity($entity);
    }

    protected function updatePage_ActiveEntity($entity)
    {
        foreach($entity->getBlocs() as $bloc){
            if(!$bloc->getType()){
                $entity->removeBloc($bloc);
            }
        }

        $this::updateEntity($entity);
    }

    /**
     * @return RedirectResponse
     */
    protected function redirectToReferrer()
    {
        $refererAction = $this->request->query->get('action');

        // from new|edit action, redirect to edit if possible
        if (in_array($refererAction, array('new', 'edit')) && $this->isActionAllowed('edit')) {
            return $this->redirectToRoute('easyadmin', array(
                'action' => 'edit',
                'entity' => $this->entity['name'],
                'menuIndex' => $this->request->query->get('menuIndex'),
                'submenuIndex' => $this->request->query->get('submenuIndex'),
                'id' => ('new' === $refererAction)
                    ? PropertyAccess::createPropertyAccessor()->getValue($this->request->attributes->get('easyadmin')['item'], $this->entity['primary_key_field_name'])
                    : $this->request->query->get('id'),
            ));
        }

        return parent::redirectToReferrer();
    }
    /**
     * @Route("", name="tableauDeBord")
     */
    public function tableauDeBordAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $repositoryLangue = $em->getRepository(Langue::class);
        $repositoryPage = $em->getRepository(Page::class);
        $user = $this->getUser();
        $locale = $request->getLocale();
        $langue = $repositoryLangue->findOneBy(array('abreviation' => $locale));

        $blocsUser = $user->getBlocsTableauDeBord();
        $blocs = [];

        if($blocsUser){
            /* Dernières pages publiées */
            if(in_array('dernieresPages', $blocsUser)){
                $dernieresPages = $em->getRepository(Page::class)->pagesPubliees($langue, 5);
                $blocs['dernieresPages'] = $dernieresPages;
            }

            /* Derniers commentaires en attente de validation */
            if(in_array('derniersCommentaires', $blocsUser)){
                $repositoryCommentaire = $em->getRepository(Commentaire::class);
                $derniersCommentaires = $repositoryCommentaire->commentairesNonValides();
                $blocs['derniersCommentaires'] = $derniersCommentaires;
            }

            /* Nombre de pages */
            if(in_array('nombreDePages', $blocsUser)){
                //Nombre total
                $nombreTotal = $repositoryPage->nombreTotal();
                $blocs['nombreDePages']['nombreTotal'] = $nombreTotal;

                //Nombre total de pages publiées
                $nombreTotalPagesPubliees = $repositoryPage->nombrePagesPubliees();
                $blocs['nombreDePages']['publiees']['nombreTotal'] = $nombreTotalPagesPubliees;

                //Nombre de pages dans chaque langue
                $langues = $repositoryLangue->findAll();
                foreach($langues as $langue){
                    $nombrePagesPublieesLangue = $repositoryPage->nombrePagesPubliees($langue);
                    $blocs['nombreDePages']['publiees']['parLangue'][$langue->getNom()] = $nombrePagesPublieesLangue;
                }
            }

            /* Liste des catégories */
            if(in_array('listeCategories', $blocsUser)){
                $repositoryTypeCategorie = $em->getRepository(TypeCategorie::class);
                $typeCategories = $repositoryTypeCategorie->findAll();
                $blocs['listeCategories'] = $typeCategories;
            }

            /* Liste des derniers inscrits */
            if(in_array('derniersInscrits', $blocsUser)){
                $repositoryUtilisateur = $em->getRepository(Utilisateur::class);
                $utilisateurs = $repositoryUtilisateur->findBy(array(), array('id' => 'ASC'), 5);
                $blocs['derniersInscrits'] = $utilisateurs;
            }
        }

        return $this->render('back/tableauDeBord.html.twig', array(
            'user' => $user,
            'blocs' => $blocs,
        ));
    }

    /**
     * @Route("/mediatheque", name="mediatheque")
     */
    public function mediathequeAction(){
        return $this->render('back/mediatheque.html.twig');
    }

    public function dupliquerAction(){
        $idPageOriginale = $this->request->query->get('id');
        $pageOriginale = $this->em->getRepository(Page::class)->find($idPageOriginale);

        $nouvellePage = clone $pageOriginale;
        $ancienSEO = $pageOriginale->getSEO();
        $nouveauSEO = clone $ancienSEO;
        $nouvellePage->setSEO($nouveauSEO);

        $nouvellePage->getSEO()->setUrl($nouvellePage->getSEO()->getUrl().'-copie');

        $menuPage = new menuPage();
        $menuPage->setPage($nouvellePage)->setPosition(0);

        $this->em->persist($nouvellePage);
        $this->em->persist($nouveauSEO);
        $this->em->persist($menuPage);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'entity' => 'Page_Active',
            'id' => $nouvellePage->getId()
        ));
    }

    public function corbeilleAction(){
        $id = $this->request->query->get('id');
        $class = substr($this->request->query->get('entity'), 0, strpos($this->request->query->get('entity'), '_'));

        $entity = $this->em->getRepository('App:'.$class)->find($id);

        if($entity->getCorbeille()){
            $entity->setCorbeille(false);

            if($class == 'Page'){
                $menuPage = new menuPage();
                $menuPage->setPage($entity)->setPosition(0);
                $this->em->persist($menuPage);
                $this->em->flush();
            }

            //Message flash
            $this->addFlash( 'success',
                'L\'élément a été restauré.'
            );
        }else{
            $entity->setCorbeille(true);

            if($class == 'Page'){
                $repoMenuPage = $this->getDoctrine()->getRepository(MenuPage::class);
                $menuPage = $repoMenuPage->findOneBy(array('page' => $entity));
                $this->em->remove($menuPage);
                $this->em->flush();
            }

            //Message flash
            $url = $this->generateUrl('admin',['action'=>'corbeille', 'entity'=>$this->request->query->get('entity'), 'id'=>$entity->getId()]);
            $this->addFlash( 'success',
                sprintf('L\'élément a été mis à la corbeille. <a href="%s">Annuler</a>', $url)
            );
        }
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
        ));
    }

    public function voirAction(){
        $entity = $this->request->query->get('entity');
        $id = $this->request->query->get('id');
        if($entity == 'Categorie'){
            $categorie = $this->em->getRepository(Categorie::class)->find($id);
            $urlCategorie = $categorie->getUrl();
            $urlTypeCategorie = $categorie->getTypeCategorie()->getUrl();
            return $this->redirectToRoute('voirCategorie', array('urlTypeCategorie' => $urlTypeCategorie, 'urlCategorie' => $urlCategorie));
        }
        else if($entity == 'TypeCategorie'){
            $typeCategorie = $this->em->getRepository(TypeCategorie::class)->find($id);
            $urlTypeCategorie = $typeCategorie->getUrl();
            return $this->redirectToRoute('voirTypeCategorie', array('urlTypeCategorie' => $urlTypeCategorie));
        }else{
            $page = $this->em->getRepository(Page::class)->find($id);
            $url = $page->getSeo()->getUrl();
            return $this->redirectToRoute('voirPage', array('url' => $url));
        }
    }

    public function activerAction(){
        $idPage = $this->request->query->get('id');
        $page = $this->em->getRepository(Page::class)->find($idPage);

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
        $this->em->persist($page);
        $this->em->flush();

        return $this->redirectToRoute('admin', array(
                'action' => 'edit',
                'entity' => 'Page_Active',
                'id' => $idPage
            )
        );
    }
}