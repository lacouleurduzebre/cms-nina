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

class ModuleController extends Controller
{
    /**
     * @Route("/admin/module/ajouterModule", name="ajouterModule")
     * @param Request $request
     * @return bool|Response
     */
    public function ajouterModuleAction(Request $request){
        if($request->isXmlHttpRequest()){
            $type = $request->get('type');

            $form = $this->get('form.factory')->createBuilder("App\Modules\\".$type."\\".$type."Type")->getForm();
            return $this->render('back/formulaireModule.html.twig', array('form'=>$form->createView()));
        };

        return false;
    }
}