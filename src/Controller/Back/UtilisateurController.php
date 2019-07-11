<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 05/07/2018
 * Time: 08:45
 */

namespace App\Controller\Back;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

/**
 * Class AdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class UtilisateurController extends BaseAdminController
{
    protected function updateEntity($entity)
    {
        parent::updateEntity($entity);

        $this->addFlash('enregistrement', "Le profil de ".$entity." a été enregistré");
    }
}