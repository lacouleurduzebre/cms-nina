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
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NinaAdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class NinaAdminController extends BaseAdminController
{
    /**
     * @Route("/tableauDeBord", name="tableauDeBord")
     */
    public function tableauDeBordAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        /* Dernières pages publiées */
        $locale = $request->getLocale();

        $repositoryLangue = $em->getRepository(Langue::class);
        $langue = $repositoryLangue->findOneBy(array('abreviation' => $locale));

        $dernieresPages = $em->getRepository(Page::class)->pagesPubliees($langue, false);

        /* Derniers commentaires en attente de validation */
        $repositoryCommentaire = $em->getRepository(Commentaire::class);

        $derniersCommentaires = $repositoryCommentaire->commentairesNonValides();

        return $this->render('back/tableauDeBord.html.twig', array(
            'pages' => $dernieresPages,
            'langue' => $langue,
            'commentaires' => $derniersCommentaires
        ));
    }

    /**
     * @Route("/mediatheque", name="mediatheque")
     */
    public function mediathequeAction(){
        return $this->render('back/mediatheque.html.twig');
    }

    public function dupliquerAction(){
        $idPageOriginale = $this->request->query->get('idPageOriginale');
        $pageOriginale = $this->em->getRepository(Page::class)->find($idPageOriginale);

        $nouvellePage = clone $pageOriginale;
        $ancienSEO = $pageOriginale->getSEO();
        $nouveauSEO = clone $ancienSEO;
        $nouvellePage->setSEO($nouveauSEO);

        $nouvellePage->getSEO()->setUrl($nouvellePage->getSEO()->getUrl().'_copie');

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
        ($entity->getCorbeille()) ?
            $entity->setCorbeille(false) : $entity->setCorbeille(true);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
        ));
    }
}