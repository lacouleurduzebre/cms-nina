<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 27/08/2018
 * Time: 09:00
 */

namespace App\Controller\Themes\terresdoh;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

//Développement Terres d'Oh!
class RechercheLEIController extends AbstractController
{
    /**
     * @Route("/{_locale}/recherche", name="rechercheLEI")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function rechercheAction(Request $request){
        $fiches = simplexml_load_file('https://apps.tourisme-alsace.info/batchs/LIENS_PERMANENTS/2002206000029_Batch_siteweb_terres_oh.xml')->Resultat->children();

        if($request->isXmlHttpRequest()){
            $data = $request->get('donnees');

            $resultat = [];
            foreach($fiches as $fiche){
                $criteres = end($fiche->CRITERES);
                $attributes = end($criteres)->attributes();
                foreach($attributes as $key => $critere){
                    foreach($data as $ligne){
                        if($critere == $ligne['value']){
                            $resultat[] = $fiche;
                            break 2;
                        }
                    }
                }
            }
            $fiches = $resultat;

            return $this->render('resultatRecherche.html.twig', array('fiches' => $fiches));
        }

        if($_POST){
            $resultat = [];
            foreach($fiches as $fiche){
                $criteres = end($fiche->CRITERES);
                $attributes = end($criteres)->attributes();
                foreach($attributes as $key => $critere){
                    if(in_array($critere, $_POST)){
                        $resultat[] = $fiche;
                        break;
                    }
                }
            }
            $fiches = $resultat;
        }

        return $this->render('recherche.html.twig', array('fiches' => $fiches, 'recherche' => $_POST, 'pageRecherche' => true));
    }

    /**
     * @Route("/{_locale}/fiche/{url}/{idFiche}", name="rechercheLEIVoirFiche")
     * @param Request $request
     * @param $idFiche
     * @param $url
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voirFicheLEIAction($url, $idFiche){
        $xml = simplexml_load_file('https://apps.tourisme-alsace.info/batchs/LIENS_PERMANENTS/2002206000029_Batch_siteweb_terres_oh.xml');

        $json = json_encode($xml);

        $php = json_decode($json);

        $fiches = $php->Resultat->sit_liste;

        $ficheRecherchee = null;
        foreach($fiches as $fiche){
            if($fiche->PRODUIT == $idFiche){
                $ficheRecherchee = $fiche;
                break;
            }
        }

        return $this->render('Blocs/LEI/fiche.html.twig', array('fiche'=>$ficheRecherchee));
    }
}