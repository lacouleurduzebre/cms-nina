<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2020-01-20
 * Time: 16:19
 */

namespace App\Controller\Back;


use App\Entity\Bloc;
use App\Entity\BlocAnnexe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Erreurs404Controller
 * @package App\Controller\Back
 * @Route("/admin")
 */
class Erreurs404Controller extends AbstractController
{
    /**
     * @Route("/erreurs404/liste", name="listeErreurs404")
     */
    public function listeErreurs404(){
        $repoBlocs = $this->getDoctrine()->getRepository(Bloc::class);
        $blocsAvecImages = $repoBlocs->blocsAvecImagesMediatheque();

        $repoBlocsAnnexes = $this->getDoctrine()->getRepository(BlocAnnexe::class);
        $blocsAnnexesAvecImages = $repoBlocsAnnexes->blocsAnnexesAvecImagesMediatheque();

        //Images
        $images404 = [];

            //Blocs
        foreach($blocsAvecImages as $bloc){
            $type = $bloc->getType();
            $contenu = $bloc->getContenu();
            if($type == 'Accordeon'){
                foreach($contenu['sections'] as $section){
                    $texte = $section['texte'];
                    $images404 = $this->rechercheImages404($images404, $bloc, $texte, true);
                }
            }elseif($type == 'Galerie'){
                foreach($contenu['images'] as $image){
                    $image = $image['image']['image'];
                    $images404 = $this->rechercheImages404($images404, $bloc, $image);
                }
            }elseif($type == 'Grille'){
                foreach($contenu['cases'] as $case){
                    $image = $case['image']['image'];
                    $images404 = $this->rechercheImages404($images404, $bloc, $image);
                    $texte = $case['texte'];
                    $images404 = $this->rechercheImages404($images404, $bloc, $texte, true);
                }
            }elseif($type == 'Image'){
                $image = $contenu['image'];
                $images404 = $this->rechercheImages404($images404, $bloc, $image);
            }elseif($type == 'HTML'){
                $texte = $contenu['code'];
                $images404 = $this->rechercheImages404($images404, $bloc, $texte, true);
            }elseif($type == 'Slider'){
                foreach($contenu['Slide'] as $slide){
                    $image = $slide['image']['image'];
                    $images404 = $this->rechercheImages404($images404, $bloc, $image);
                }
            }elseif($type == 'Texte'){
                $texte = $contenu['texte'];
                $images404 = $this->rechercheImages404($images404, $bloc, $texte, true);
            }
        }

            //Blocs annexes
        foreach($blocsAnnexesAvecImages as $bloc){
            $type = $bloc->getType();
            $contenu = $bloc->getContenu();
            if($type == 'Bandeau' or $type == 'Vignette'){
                $image = $contenu['image']['image'];
                $images404 = $this->rechercheImages404($images404, $bloc, $image);
            }elseif($type == 'Resume'){
                $texte = $contenu['resume'];
                $images404 = $this->rechercheImages404($images404, $bloc, $texte, true);
            }
        }

        //Liens
        //@Todo tester liens balises href

        return $this->render('back/erreurs404.html.twig', ['images404' => $images404]);
    }

    private function rechercheImages404($images404, $bloc, $element, $regex = false){
        if($regex){
            preg_match_all('/<img( [a-z]+="[^"]+")* src="(\/uploads\/[^"]*)"/i', $element, $images, PREG_SET_ORDER);

            foreach($images as $image){
                if(!file_exists(getcwd().$image[2]) && !file_exists(getcwd().urldecode($image[2]))){
                    if(!key_exists('page'.$bloc->getPage()->getId(), $images404)){
                        $images404['page'.$bloc->getPage()->getId()]['page'] = $bloc->getPage();
                    }
                    $images404['page'.$bloc->getPage()->getId()]['erreurs'][] = ['bloc' => $bloc, 'url' => $image[2]];
                }
            }
        }else{
            if(!file_exists(getcwd().$element) && !file_exists(getcwd().urldecode($element))){
                if(!key_exists('page'.$bloc->getPage()->getId(), $images404)){
                    $images404['page'.$bloc->getPage()->getId()]['page'] = $bloc->getPage();
                }
                $images404['page'.$bloc->getPage()->getId()]['erreurs'][] = ['bloc' => $bloc, 'url' => $element];
            }
        }

        return $images404;
    }
}