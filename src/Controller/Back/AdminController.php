<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 19/02/2018
 * Time: 10:28
 */

namespace App\Controller\Back;

use App\Entity\Commentaire;
use App\Entity\Langue;
use App\Entity\Page;
use App\Entity\TypeCategorie;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class AdminController extends BaseAdminController
{
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

        /* Dernières pages publiées */
        if(in_array('dernieresPages', $blocsUser)){
            $dernieresPages = $em->getRepository(Page::class)->pagesPubliees($langue);
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

        $this->em->persist($nouvellePage);
        $this->em->persist($nouveauSEO);
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

            //Message flash
            $this->addFlash( 'success',
                'L\'élément a été restauré.'
            );
        }else{
            $entity->setCorbeille(true);

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
        $idPage = $this->request->query->get('id');
        $page = $this->em->getRepository(Page::class)->find($idPage);

        $url = $page->getSeo()->getUrl();

        return $this->redirectToRoute('voirPage', array('url' => $url));
    }
}