<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\MenuPage;
use App\Service\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/{url}", name="voirPage")
     * @Route("/{_locale}/{url}", name="voirPageLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voirAction(Request $request, Page $spage, \App\Service\Langue $slangue, $url, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('voirPage', $_locale, array('url' => $url));
        if($redirection){
            return $redirection;
        }

        $page = $spage->getPageActive();

        if($page instanceof RedirectResponse){
            return $page;
        }

        if(!($page instanceof \App\Entity\Page)){
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
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

        /* Menus */
        $repoMenuPages = $this->getDoctrine()->getRepository(MenuPage::class);
        $menusPages = $repoMenuPages->findBy(array('page' => $page));
        $menus = [];
        foreach($menusPages as $menuPage){
            $menu = $menuPage->getMenu();
            if($menu){
                $menus[] = $menu->getId();
            }
        }
        $menus = array_unique($menus);
        /* Fin menus */

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
                if($form->isSubmitted() && $form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($commentaire);
                    $em->flush();

                    $this->addFlash('comOK', 'Votre commentaire a été enregistré et sera mis en ligne une fois validé');

                    return $this->redirectToRoute('voirPage', array('url' => $url));
                }
            }
        /* Fin commentaires */

        return $this->render('front/page.html.twig', array('page'=>$page, 'form'=>$form->createView(), 'commentaires'=>$commentaires, 'menusDeLaPage'=>$menus));
    }
}
