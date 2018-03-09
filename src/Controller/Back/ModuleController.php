<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 09/03/2018
 * Time: 09:17
 */

namespace App\Controller\Back;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends Controller
{
    /**
     * @Route("/admin/choixTypeModule", name="choisirTypeModule")
     * @param Request $request
     */
    public function choisirTypeModuleAction(Request $request){
        $typeModule = $request->get('TypeModule');

        $class = 'App\Entity\Modules\Module'.$typeModule;

        $module = new $class;

        $form = $this->get('form.factory')->createBuilder('App\Form\Type\Modules\Module'.$typeModule.'Type', $module)->getForm();

        return $this->render('back/modules/formulaireModule.html.twig', array('typeModule' => $typeModule, 'form'=>$form->createView()));
    }

    /**
     * @Route("/admin/enregistrerModule/{typeModule}", name="enregistrerModule")
     * @param Request $request
     */
    public function enregistrerModule($typeModule, Request $request){
        $class = 'App\Entity\Modules\Module'.$typeModule;

        $module = new $class;

        $form = $this->get('form.factory')->createBuilder('App\Form\Type\Modules\Module'.$typeModule.'Type', $module)->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($module);
            $em->flush();

            return new Response($module->getId());
        }

        return new Response('pas ok');
    }
}