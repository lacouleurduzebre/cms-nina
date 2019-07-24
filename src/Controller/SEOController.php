<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-03-29
 * Time: 14:21
 */

namespace App\Controller;


use App\Entity\Bloc;
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
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');
            $donnees = $request->get('donnees');
            $SEO = $this->getSEO($id);

            $form = $this->createForm(SEOType::class, $SEO)
                ->add('Enregistrer', SubmitType::class);

            if(!$donnees){
                $tpl = $this->render('back/easyadmin/SEO/_SEO-edition.html.twig', array('form' => $form->createView()))->getContent();
            }else{
                $SEO->setMetaTitre($donnees[0]['value']);
                $SEO->setUrl($donnees[1]['value']);
                $SEO->setMetaDescription($donnees[2]['value']);

                $em->persist($SEO);
                $em->flush();

                $tpl = $this->render('back/easyadmin/SEO/_SEO-apercu.html.twig', array('item' => $SEO))->getContent();
            }

            return new Response($tpl);
        }

        return new Response(false);
    }

    /**
     * @Route("/admin/seo/raz", name="razSEO")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function raz(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');
            $SEO = $this->getSEO($id);

            $page = $SEO->getPage();

            //Méta-titre
            $titre = $page->getTitre();
            $SEO->setMetaTitre(substr($titre, 0, 65));

            //Méta-description
            $repoBloc = $this->getDoctrine()->getRepository(Bloc::class);
            $blocTexte = $repoBloc->premierBlocTexte($page);
            if($blocTexte){
                $SEO->setMetaDescription(substr(strip_tags($blocTexte[0]->getContenu()['texte']), 0, 150));
            }else{
                $SEO->setMetaDescription(substr($titre, 0, 150));
            }

            //Url
            $url = $this->slugify($titre);
            $SEO->setUrl(substr($url, 0, 75));

            //Enregistrement
            $em->persist($SEO);
            $em->flush();

            $tpl = $this->render('back/easyadmin/SEO/_SEO-apercu.html.twig', array('item' => $SEO))->getContent();

            return new Response($tpl);
        }

        return new Response(false);
    }

    private function getSEO($id){
        $doctrine = $this->getDoctrine();

        $repoSEO = $doctrine->getRepository(SEO::class);
        $SEO = $repoSEO->find($id);

        return $SEO;
    }

    public static function slugify($string, $delimiter = '-') {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);
        return $clean;
    }
}