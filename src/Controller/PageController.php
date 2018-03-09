<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\SEO;
use App\Service\Front\ContenuModule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends Controller
{
    /**
     * @Route("/{url}", name="voirPage")
     * @param Request $request
     * @param $url
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voirAction(Request $request, ContenuModule $serviceModule, $url){
        $repository = $this->getDoctrine()->getRepository(SEO::class);
        $seo = $repository->findOneByUrl($url);

        if(!$seo){
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }

        $page = $seo->getPage();

        $timestamp = new \DateTime();
        $date = $timestamp->format('Y-m-d H:i:s');

        if(!$page->getDatePublication() < $date && $page->getDateDepublication() > $date && $page->getCorbeille()=="0" && $page->getActive()=="1") {
            throw new NotFoundHttpException('Cette page n\'est plus accessible');
        }

        $commentaires = $page->getCommentaires();

        $commentaire = new Commentaire();

        $utilisateur = $this->getUser();
        if ($utilisateur){
            $commentaire->setAuteur($utilisateur->getUsername());
            $commentaire->setEmail($utilisateur->getEmail());
        }else{
            $commentaire->setAuteur('Anonyme');
        }

        /* Modules */
            $modules = $page->getModules();
            $contenusModules = [];
            $i=0;
            foreach($modules as $module){
                /* service ContenuModule */
                 $contenuModule = $serviceModule->getContenuModule($module->getType(), $module->getIdModule());

                 $contenusModules[$i]['type']=$module->getType();
                 $contenusModules[$i]['contenu']=$contenuModule;

                 $i++;
            }
        /* Fin modules */

        /* Commentaires */
            $commentaire->setPage($page);

            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $commentaire);

            $formBuilder
                ->add('auteur', TextType::class, array('label' => 'Votre nom :'))
                ->add('email', EmailType::class, array('label' => 'Votre adresse e-mail :'))
                ->add('site', TextType::class, array('label' => 'Votre site web :', 'required' => false))
                ->add('contenu', TextareaType::class, array('label'=>'Votre commentaire :'))
                ->add('envoi', SubmitType::class, array('attr'=>array('class'=>'envoiCom')));

            $form=$formBuilder->getForm();

            if($request->isMethod('POST')){
                $form->handleRequest($request);
                if($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($commentaire);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('comOK', 'Votre commentaire a été enregistré et sera mis en ligne une fois validé');

                    return $this->redirectToRoute('voirPage', array('url' => $seo->getUrl()));
                }
            }
        /* Fin commentaires */

        return $this->render('front/voirPage.html.twig', array('page'=>$page, 'form'=>$form->createView(), 'commentaires'=>$commentaires, 'modules'=>$contenusModules));
    }
}
