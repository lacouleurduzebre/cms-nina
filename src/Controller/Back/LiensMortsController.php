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
use App\Entity\Configuration;
use App\Entity\Utilisateur;
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
        //Images dans des blocs
        $imagesBlocs = [];
        $repoBlocs = $this->getDoctrine()->getRepository(Bloc::class);
        $repoBlocsAnnexes = $this->getDoctrine()->getRepository(BlocAnnexe::class);

            //Bandeaux
        $blocsBandeaux = $repoBlocsAnnexes->findBy(['type' => 'Bandeau']);
        foreach($blocsBandeaux as $bloc){
            $urlImage = $bloc->getContenu()['image']['image'];
            $imagesBlocs = $this->testUrlBloc($urlImage, $bloc, $imagesBlocs);
        }

            //Galeries
        $blocsGalerie = $repoBlocs->findBy(['type' => 'Galerie']);
        foreach($blocsGalerie as $bloc){
            foreach($bloc->getContenu()['images'] as $image){
                $urlImage = $image['image']['image'];
                $imagesBlocs = $this->testUrlBloc($urlImage, $bloc, $imagesBlocs);
            }
        }

            //Grilles
        $blocsGrille = $repoBlocs->findBy(['type' => 'Grille']);
        foreach($blocsGrille as $bloc){
            foreach($bloc->getContenu()['cases'] as $case){
                $urlImage = $case['image']['image'];
                $imagesBlocs = $this->testUrlBloc($urlImage, $bloc, $imagesBlocs);
            }
        }

            //Images
        $blocsImages = $repoBlocs->findBy(['type' => 'Image']);
        foreach($blocsImages as $bloc){
            $urlImage = $bloc->getContenu()['image'];
            $imagesBlocs = $this->testUrlBloc($urlImage, $bloc, $imagesBlocs);
        }

            //Sliders
        $blocsSliders = $repoBlocs->findBy(['type' => 'Slider']);
        foreach($blocsSliders as $bloc){
            foreach($bloc->getContenu()['Slide'] as $slide){
                $urlImage = $slide['image']['image'];
                $imagesBlocs = $this->testUrlBloc($urlImage, $bloc, $imagesBlocs);
            }
        }

            //Vignettes
        $blocsVignettes = $repoBlocsAnnexes->findBy(['type' => 'Vignette']);
        foreach($blocsVignettes as $bloc){
            $urlImage = $bloc->getContenu()['image']['image'];
            $imagesBlocs = $this->testUrlBloc($urlImage, $bloc, $imagesBlocs);
        }

        //Autres images
        $imagesAutres = [];

            //Images de profil
        $utilisateurs = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
        foreach($utilisateurs as $utilisateur){
            if(!$this->testUrl($utilisateur->getImageProfil())){
                $imagesAutres[] = [
                    'typeEntite' => 'Utilisateur',
                    'idEntite' => $utilisateur->getId(),
                    'origine' => 'Image de profil de "'.$utilisateur->getUsername().'"',
                    'lien' => $urlImage
                ];
            }
        }

            //Logo du site
        $config = $this->getDoctrine()->getRepository(Configuration::class)->find(1);
        if(!$this->testUrl($config->getLogo())){
            $imagesAutres[] = [
                'typeEntite' => 'Configuration',
                'idEntite' => 1,
                'origine' => 'Logo du site',
                'lien' => $urlImage
            ];
        }

        //Liens dans des blocs
        $liensBlocs = [];

            //Boutons
        $blocsBoutons = $repoBlocs->findBy(['type' => 'Bouton']);
        foreach($blocsBoutons as $bloc){
            $url = $bloc->getContenu()['lien'];
            $liensBlocs = $this->testUrlBloc($url, $bloc, $liensBlocs);
        }

            //Galeries
        $blocsGalerie = $repoBlocs->findBy(['type' => 'Galerie']);
        foreach($blocsGalerie as $bloc){
            foreach($bloc->getContenu()['images'] as $image){
                $url = $image['lien'];
                $liensBlocs = $this->testUrlBloc($url, $bloc, $liensBlocs);
            }
        }

            //Grilles
        $blocsGrille = $repoBlocs->findBy(['type' => 'Grille']);
        foreach($blocsGrille as $bloc){
            foreach($bloc->getContenu()['cases'] as $case){
                $url = $case['lien']['lien'];
                $liensBlocs = $this->testUrlBloc($url, $bloc, $liensBlocs);
            }
        }

            //Images
        $blocsImage = $repoBlocs->findBy(['type' => 'Image']);
        foreach($blocsImage as $bloc){
            $url = $bloc->getContenu()['lien'];
            $liensBlocs = $this->testUrlBloc($url, $bloc, $liensBlocs);
        }

            //Réseaux sociaux
        $blocsReseauxSociaux = $repoBlocs->findBy(['type' => 'ReseauxSociaux']);
        foreach($blocsReseauxSociaux as $bloc){
            foreach($bloc->getContenu() as $champ => $valeur){
                if(substr($champ, strlen($champ) - 3, 3) == 'Url'){
                    $liensBlocs = $this->testUrlBloc($valeur, $bloc, $liensBlocs);
                }
            }
        }

            //Sliders
        $blocsSliders = $repoBlocs->findBy(['type' => 'Slider']);
        foreach($blocsSliders as $bloc){
            foreach($bloc->getContenu()['Slide'] as $slide){
                $url = $slide['lien'];
                $liensBlocs = $this->testUrlBloc($url, $bloc, $liensBlocs);
            }
        }

            //Vidéos
        $blocsVideos = $repoBlocs->findBy(['type' => 'Video']);
        foreach($blocsVideos as $bloc){
            $url = $bloc->getContenu()['video'];
            $liensBlocs = $this->testUrlBloc($url, $bloc, $liensBlocs);
        }

        return $this->render('back/liensMorts/liensMorts.html.twig', ['imagesBlocs' => $imagesBlocs, 'imagesAutres' => $imagesAutres, 'liensBlocs' => $liensBlocs]);
    }

    public function testUrl($url){
        if(substr($url, 0, 1) == '/'){
            return file_exists(getcwd().$url);
        }else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $headers = curl_getinfo($ch);
            curl_close($ch);

            return ($headers['http_code'] === '404');
        }
    }

    private function testUrlBloc($url, $bloc, $tableau){
        if($url != '' && !$this->testUrl($url)){
            $tableau[] = [
                'page' => $bloc->getPage(),
                'groupeBlocs' => method_exists($bloc, 'getGroupeBlocs') ? $bloc->getGroupeBlocs() : null,
                'typeBloc' => $bloc->getType(),
                'lien' => $url,
                'idBloc' => $bloc->getId()
            ];
        };

        return $tableau;
    }
}