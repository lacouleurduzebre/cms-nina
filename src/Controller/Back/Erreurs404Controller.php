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
    private $erreurs;
    private $bloc;
    private $element;

    public function __construct()
    {
        $this->erreurs = [];
        $this->bloc = null;
        $this->element = null;
    }

    /**
     * @Route("/erreurs404/liste", name="listeErreurs404")
     */
    public function listeErreurs404(){
        $repoBlocs = $this->getDoctrine()->getRepository(Bloc::class);
        $blocsAvecLiens = $repoBlocs->blocsAvecLiensMediatheque();

        $repoBlocsAnnexes = $this->getDoctrine()->getRepository(BlocAnnexe::class);
        $blocsAnnexesAvecLiens = $repoBlocsAnnexes->blocsAnnexesAvecLiensMediatheque();

        //Blocs
        foreach($blocsAvecLiens as $bloc){
            $this->bloc = $bloc;
            $type = $this->bloc->getType();
            $contenu = $this->bloc->getContenu();
            if($type == 'Accordeon'){
                foreach($contenu['sections'] as $section){
                    $this->element = $section['texte'];
                    $this->rechercheLiens404(true, 'fichiers404');
                    $this->rechercheLiens404(true, 'images404');
                }
            }elseif($type == 'Bouton'){
                $this->element = $contenu['lien'];
                $this->rechercheLiens404(false, 'fichiers404');
            }elseif($type == 'Galerie'){
                foreach($contenu['images'] as $image){
                    $this->element = $image['lien'];
                    $this->rechercheLiens404(false, 'fichiers404');
                    $this->element = $image['image']['image'];
                    $this->rechercheLiens404(false, 'images404');
                }
            }elseif($type == 'Grille'){
                foreach($contenu['cases'] as $case){
                    $this->element = $case['image']['image'];
                    $this->rechercheLiens404(false, 'images404');
                    $this->element = $case['texte'];
                    $this->rechercheLiens404(true, 'images404');
                    $this->rechercheLiens404(true, 'fichiers404');
                    $this->element = $case['lien']['lien'];
                    $this->rechercheLiens404(false, 'fichiers404');
                }
            }elseif($type == 'Image'){
                $this->element = $contenu['image'];
                $this->rechercheLiens404(false, 'images404');
                $this->element = $contenu['lien'];
                $this->rechercheLiens404(false, 'fichiers404');
            }elseif($type == 'HTML'){
                $this->element = $contenu['code'];
                $this->rechercheLiens404(true, 'images404');
                $this->rechercheLiens404(true, 'fichiers404');
            }elseif($type == 'Paragraphe'){
                $this->element = $contenu['texte'];
                $this->rechercheLiens404(true, 'fichiers404');
            }elseif($type == 'Slider'){
                foreach($contenu['Slide'] as $slide){
                    $this->element = $slide['image']['image'];
                    $this->rechercheLiens404(false, 'images404');
                    $this->element = $slide['texte'];
                    $this->rechercheLiens404(true, 'fichiers404');
                    $this->element = $slide['lien'];
                    $this->rechercheLiens404(false, 'fichiers404');
                }
            }elseif($type == 'Texte'){
                $this->element = $contenu['texte'];
                $this->rechercheLiens404(true, 'images404');
                $this->rechercheLiens404(true, 'fichiers404');
            }
        }

        //Blocs annexes
        foreach($blocsAnnexesAvecLiens as $bloc){
            $this->bloc = $bloc;
            $type = $this->bloc->getType();
            $contenu = $this->bloc->getContenu();
            if($type == 'Bandeau' or $type == 'Vignette'){
                $this->element = $contenu['image']['image'];
                $this->rechercheLiens404(false, 'images404');
            }elseif($type == 'Resume'){
                $this->element = $contenu['resume'];
                $this->rechercheLiens404(true, 'images404');
                $this->rechercheLiens404(true, 'fichiers404');
            }
        }

        return $this->render('back/erreurs404.html.twig', ['erreurs' => $this->erreurs]);
    }

    private function rechercheLiens404($regex, $type){
        if(!key_exists($type, $this->erreurs)){
            $this->erreurs[$type] = [];
        }

        if($regex){
            $regex = ($type == 'fichiers404') ? '/<a( [a-z]+="[^"]+")* href="(\/uploads\/[^"]*)"/i' : '/<img( [a-z]+="[^"]+")* src="(\/uploads\/[^"]*)"/i';
            preg_match_all($regex, $this->element, $liens, PREG_SET_ORDER);

            foreach($liens as $lien){
                $this->element = $lien[2];
                $this->testLien($type);
            }
        }else{
            $this->testLien($type);
        }
    }

    private function testLien($type){
        if(substr($this->element, 0, 4) == 'http'){
            $type = 'liensExternes404';
            $file_headers = @get_headers($this->element);
            if(!$file_headers[0] == 'HTTP/1.0 404 Not Found' || !($file_headers[0] == 'HTTP/1.0 302 Found' && $file_headers[7] == 'HTTP/1.0 404 Not Found')){
                return;
            }
        }else{
            if(file_exists(getcwd().$this->element) || file_exists(getcwd().urldecode($this->element))){
                return;
            }
        }

        if(!key_exists('page'.$this->bloc->getPage()->getId(), $this->erreurs[$type])){
            $this->erreurs[$type]['page'.$this->bloc->getPage()->getId()]['page'] = $this->bloc->getPage();
        }
        $this->erreurs[$type]['page'.$this->bloc->getPage()->getId()]['erreurs'][] = ['bloc' => $this->bloc, 'url' => $this->element];
    }
}