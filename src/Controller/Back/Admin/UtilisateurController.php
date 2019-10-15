<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 05/07/2018
 * Time: 08:45
 */

namespace App\Controller\Back\Admin;

use App\Controller\Back\AdminController;
use App\Service\Droits;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class UtilisateurController extends AdminController
{
    private $droits;

    public function __construct(Droits $droits)
    {
        $this->droits = $droits;
    }

    protected function listAction()
    {
        if(!$this->droits->checkDroit('comptes')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        return parent::listAction();
    }

    protected function searchAction()
    {
        if(!$this->droits->checkDroit('comptes')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        return parent::listAction();
    }

    protected function showAction()
    {
        if(!$this->droits->checkDroit('comptes')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        return parent::showAction();
    }

    protected function newAction($listeBlocs = false)
    {
        if(!$this->droits->checkDroit('comptes')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        return parent::newAction($listeBlocs);
    }

    protected function deleteAction()
    {
        if(!$this->droits->checkDroit('comptes')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        return parent::deleteAction();
    }

    protected function editAction($listeBlocs = false)
    {
        $id = $this->request->query->get('id');
        $idUser = $this->getUser()->getId();

        if(!$this->droits->checkDroit('comptes') && $id != $idUser){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        return parent::editAction($listeBlocs);
    }

    protected function updateEntity($entity)
    {
        parent::updateEntity($entity);

        $this->addFlash('enregistrement', "Le profil de ".$entity." a été enregistré");
    }
}