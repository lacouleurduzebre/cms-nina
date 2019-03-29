<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-03-29
 * Time: 14:21
 */

namespace App\Controller;


use App\Entity\SEO;
use App\Form\Type\SEOType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SEOController extends Controller
{
    /**
     * @Route("/admin/seo/edition", name="editerSEO")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edition(Request $request){
        if($request->isXmlHttpRequest()){
            //Création du formulaire
            $id = $request->get('id');

            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();

            $repoSEO = $doctrine->getRepository(SEO::class);
            $SEO = $repoSEO->find($id);

            $form = $this->createForm(SEOType::class, $SEO)
                ->add('Enregistrer', SubmitType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $SEO = $form->getData();

                $em->persist($SEO);
                $em->flush();
            }


            $tpl = $this->render('back/formulaire.html.twig', array('form' => $form->createView()))->getContent();

            return new Response($tpl);
        }

        return new Response(false);
    }
}