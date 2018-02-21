<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 19/02/2018
 * Time: 10:28
 */

namespace App\Controller\Back;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class AdminController extends BaseAdminController
{
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