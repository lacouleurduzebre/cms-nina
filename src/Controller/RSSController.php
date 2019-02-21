<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 18/02/2019
 * Time: 14:54
 */

namespace App\Controller;


use App\Entity\BlocAnnexe;
use App\Entity\Langue;
use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RSSController extends Controller
{
    /**
     * @Route("/{_locale}/rss.xml", defaults={"_format"="xml"}, name="rss")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function rssAction(Request $request){
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);
        $langue = $repoLangue->findOneBy(array('abreviation' => $request->getLocale()));

        $repoPage = $this->getDoctrine()->getRepository(Page::class);
        $pages = $repoPage->pagesPublieesCategorie(0, $langue, 10);

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