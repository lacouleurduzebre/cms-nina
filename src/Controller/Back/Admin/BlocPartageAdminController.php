<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 26/02/2019
 * Time: 14:42
 */

namespace App\Controller\Back\Admin;


use App\Controller\Back\AdminController;
use App\Entity\Bloc;
use App\Entity\BlocPartage;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityRemoveException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlocPartageAdminController
 * @package App\Controller\Back\Admin
 * @Route("/admin")
 */
class BlocPartageAdminController extends AdminController
{
    /**
     * The method that is executed when the user performs a 'delete' action to
     * remove any entity.
     *
     * @return RedirectResponse
     *
     * @throws EntityRemoveException
     */
    protected function deleteBlocPartageAction(){
        $blocPartage = $this->getDoctrine()->getRepository(BlocPartage::class)->find($this->request->query->get('id'));
        $idBloc = $blocPartage->getBloc()->getId();
        $repoBloc = $this->getDoctrine()->getRepository(Bloc::class);
        $em = $this->getDoctrine()->getManager();

        //Suppression du bloc partagé
        $this->deleteAction();

        //Suppression des blocs partagés associés à ce bloc
        $blocsPartages = $repoBloc->findBy(['type' => 'BlocPartage']);

        foreach($blocsPartages as $blocPartage){
            if($blocPartage->getContenu()['blocPartage'] == $idBloc){
                $em->remove($blocPartage);
            }
        }

        //Suppression du bloc
        $bloc = $repoBloc->find($idBloc);
        $em->remove($bloc);

        $em->flush();

        return $this->redirectToReferrer();
    }
}