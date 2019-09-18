<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/08/2018
 * Time: 15:59
 */

namespace App\Controller\Back;


use App\Entity\Bloc;
use App\Entity\BlocAnnexe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LiensMortsController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class LiensMortsController extends AbstractController
{
    /**
     * @Route("/liensMorts", name="liensMorts")
     */
    public function liensMortsAction(){
        //Images
        $images = [];
        $repoBlocs = $this->getDoctrine()->getRepository(Bloc::class);
        $repoBlocsAnnexes = $this->getDoctrine()->getRepository(BlocAnnexe::class);

            //Bandeaux
        $blocsBandeaux = $repoBlocsAnnexes->findBy(['type' => 'Bandeau']);
        foreach($blocsBandeaux as $bloc){
            $urlImage = $bloc->getContenu()['image']['image'];
            $images = $this->testUrlImage($urlImage, $bloc, $images);
        }

            //Galeries
        $blocsGalerie = $repoBlocs->findBy(['type' => 'Galerie']);
        foreach($blocsGalerie as $bloc){
            foreach($bloc->getContenu()['images'] as $image){
                $urlImage = $image['image']['image'];
                $images = $this->testUrlImage($urlImage, $bloc, $images);
            }
        }

            //Grilles
        $blocsGrille = $repoBlocs->findBy(['type' => 'Grille']);
        foreach($blocsGrille as $bloc){
            foreach($bloc->getContenu()['cases'] as $case){
                $urlImage = $case['image']['image'];
                $images = $this->testUrlImage($urlImage, $bloc, $images);
            }
        }

            //Images
        $blocsImages = $repoBlocs->findBy(['type' => 'Image']);
        foreach($blocsImages as $bloc){
            $urlImage = $bloc->getContenu()['image'];
            $images = $this->testUrlImage($urlImage, $bloc, $images);
        }

            //Sliders
        $blocsSliders = $repoBlocs->findBy(['type' => 'Slider']);
        foreach($blocsSliders as $bloc){
            foreach($bloc->getContenu()['Slide'] as $slide){
                $urlImage = $slide['image']['image'];
                $images = $this->testUrlImage($urlImage, $bloc, $images);
            }
        }

            //Vignettes
        $blocsVignettes = $repoBlocsAnnexes->findBy(['type' => 'Vignette']);
        foreach($blocsVignettes as $bloc){
            $urlImage = $bloc->getContenu()['image']['image'];
            $images = $this->testUrlImage($urlImage, $bloc, $images);
        }

        return $this->render('back/liensMorts/liensMorts.html.twig', ['images' => $images]);
    }

    public function testUrl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        return $headers['http_code'];
    }

    private function testUrlImage($urlImage, $bloc, $tableau){
        $site = $_SERVER['SERVER_NAME'];

        if(substr($urlImage, 0, 8) == '/uploads'){
            $urlImage = $site.$urlImage;
        }


        if($this->testUrl($urlImage) == '404'){
            $tableau = $this->addLienMort($urlImage, $bloc, $tableau);
        };

        return $tableau;
    }

    private function addLienMort($lien, $bloc, $tableau){
        $tableau[] = [
            'page' => $bloc->getPage(),
            'groupeBlocs' => method_exists($bloc, 'getGroupeBlocs') ? $bloc->getGroupeBlocs() : null,
            'typeBloc' => $bloc->getType(),
            'lien' => $lien,
            'idBloc' => $bloc->getId()
        ];

        return $tableau;
    }
}