<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-03-29
 * Time: 14:21
 */

namespace App\Controller;


use App\Entity\SEO;
use App\Entity\SEOCategorie;
use App\Entity\SEOPage;
use App\Entity\SEOTypeCategorie;
use App\Form\Type\SEOType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

class SEOController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/admin/seo', name: 'referencement')]
    public function liste(){
        $repoSEOPage = $this->doctrine->getRepository(SEOPage::class);
        $SEOPage = $repoSEOPage->SEOPagesHorsCorbeille();

        $repoSEOCategorie = $this->doctrine->getRepository(SEOCategorie::class);
        $SEOCategorie = $repoSEOCategorie->findAll();

        $repoSEOTypeCategorie = $this->doctrine->getRepository(SEOTypeCategorie::class);
        $SEOTypeCategorie = $repoSEOTypeCategorie->findAll();

        $SEO = ['Pages' => $SEOPage, 'Catégories' => $SEOCategorie, 'Types de catégories' => $SEOTypeCategorie];

        return $this->render('back/SEO/listeSEO.html.twig', ['SEO' => $SEO]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/admin/seo/edition', name: 'editerSEO')]
    public function edition(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->doctrine->getManager();

            $id = $request->request->get('id');
            $type = $request->request->get('type');
            $donnees = $request->request->all('donnees');

            $SEO = $this->getSEO($type, $id);

            $form = $this->createForm(SEOType::class, $SEO)
                ->add('Enregistrer', SubmitType::class);

            if(!$donnees){
                $tpl = $this->render('back/SEO/_SEO-edition.html.twig', array('form' => $form->createView()))->getContent();
            }else{
                $SEO->setMetaTitre($donnees[0]['value']);
                $SEO->setUrl($donnees[1]['value']);
                $SEO->setMetaDescription($donnees[2]['value']);

                $em->persist($SEO);
                $em->flush();

                $tpl = $this->render('back/SEO/_SEO-apercu.html.twig', array('seo' => $SEO))->getContent();
            }

            return new Response($tpl);
        }

        return new Response(false);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/admin/seo/raz', name: 'razSEO')]
    public function raz(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->doctrine->getManager();

            $id = $request->request->get('id');
            $type = $request->request->get('type');

            $SEO = $this->getSEO($type, $id);

            if($type == 'pages'){
                $page = $SEO->getPage();

                $titre = $page->getTitre();
                $description = $this->renderView('front/blocs.html.twig', ['blocs' => $page->getBlocs()]);
                $description = trim(strip_tags($description));
                if($description == ''){
                    $description = $titre;
                }
            }elseif($type == 'categories'){
                $categorie = $SEO->getCategorie();
                $description = $titre = $categorie->getNom();
                if($categorie->getDescription()){
                    $description = strip_tags($categorie->getDescription());
                }
            }else{
                $typeCategorie = $SEO->getTypeCategorie();
                $description = $titre = $typeCategorie->getNom();
                if($typeCategorie->getDescription()){
                    $description = strip_tags($typeCategorie->getDescription());
                }
            }

            $SEO->setMetaTitre(substr($titre, 0, 65));

            $SEO->setMetaDescription(substr($description, 0, 150));

            $url = $this->slugify($titre);
            $SEO->setUrl(substr($url, 0, 75));

            //Enregistrement
            $em->persist($SEO);
            $em->flush();

            $tpl = $this->render('back/SEO/_SEO-apercu.html.twig', array('seo' => $SEO))->getContent();

            return new Response($tpl);
        }

        return new Response(false);
    }

    private function getSEO($type, $id){
        $doctrine = $this->doctrine;

        if($type == 'pages'){
            $repoSEO = $doctrine->getRepository(SEOPage::class);
        }elseif($type == 'categories'){
            $repoSEO = $doctrine->getRepository(SEOCategorie::class);
        }else{
            $repoSEO = $doctrine->getRepository(SEOTypeCategorie::class);
        }

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