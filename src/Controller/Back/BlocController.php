<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/07/2018
 * Time: 14:47
 */

namespace App\Controller\Back;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlocController extends Controller
{
    /**
     * @Route("/admin/bloc/ajouterBloc", name="ajouterBloc")
     * @param Request $request
     * @return bool|Response
     */
    public function ajouterBlocAction(Request $request){
        if($request->isXmlHttpRequest()){
            $type = $request->get('type');

            $form = $this->get('form.factory')->createBuilder("App\Form\Type\BlocType", null, array('type' => $type))->getForm();
            return $this->render('back/formulaireBloc.html.twig', array('form'=>$form->createView()));
        };

        return false;
    }
}