<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 18/02/2019
 * Time: 14:54
 */

namespace App\Controller;


use App\Entity\BlocAnnexe;
use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RSSController extends AbstractController
{
    /**
     * @Route("/rss.xml", defaults={"_format"="xml"}, name="rss")
     * @Route("/{_locale}/rss.xml", defaults={"_format"="xml"}, name="rssLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function rssAction(\App\Service\Langue $slangue, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('rss', $_locale);
        if($redirection){
            return $redirection;
        }

        $repoPage = $this->getDoctrine()->getRepository(Page::class);

        //Nombre de pages à afficher dans la config
        $repoConfig = $this->getDoctrine()->getRepository(Configuration::class);
        $config = $repoConfig->find(1);
        $nbPages = $config->getNbArticlesFluxRSS();

        $nbPages = $nbPages ?? 20;

        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);
        $langue = $repoLangue->findOneBy(array('abreviation' => $_locale));
        if(!$langue){
            $langue = $repoLangue->findOneBy(array('defaut' => true));
        }
        $pages = $repoPage->pagesPublieesCategorie(0, $langue, $nbPages);

        $repoBlocAnnexe = $this->getDoctrine()->getRepository(BlocAnnexe::class);
        $vignettes = [];
        foreach($pages as $page){
            $vignette = $repoBlocAnnexe->findOneBy(array('page' => $page, 'type' => 'Vignette'));

            if($vignette){
                $image = $this->getParameter('kernel.project_dir').'/public'.$vignette->getContenu()['image']['image'];
                if(is_file($image)){
                    $vignettes[$page->getId()]['taille'] = filesize($image);
                    $vignettes[$page->getId()]['type'] = filetype($image);
                }
            }
        }

        return $this->render('front/rss.xml.twig', array('pages' => $pages, 'vignettes' => $vignettes));
    }
}