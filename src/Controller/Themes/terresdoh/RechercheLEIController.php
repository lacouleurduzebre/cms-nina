<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 27/08/2018
 * Time: 09:00
 */

namespace App\Controller\Themes\terresdoh;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $donnees = [];

        if($request->isXmlHttpRequest()){
            $data = $request->get('donnees');
            $reponse = [];
            $resultat = [];

            foreach($fiches as $fiche){
                $criteres = end($fiche->CRITERES);
                $attributes = end($criteres)->attributes();
                foreach($attributes as $key => $critere){
                    foreach($data as $ligne){
                        if($critere == $ligne['value']){
                            $resultat[] = $fiche;
                            $reponse['fiches'][] = [
                                'numero' => (string)$fiche->PRODUIT,
                                'lat' => str_replace(',', '.', $fiche->LATITUDE),
                                'lng' => str_replace(',', '.', $fiche->LONGITUDE),
                                'titre' => (string)$fiche->NOM,
                                'image' => (string)$fiche->CRITERES->Crit[0],
                                'lien' => str_replace(' ', '-', $fiche->NOM)
                            ];
                            break 2;
                        }
                    }
                }
            }
            $fiches = $resultat;

            $reponse['template'] = $this->render('Blocs/LEI/liste.html.twig', array('fiches' => $fiches))->getContent();
            json_encode($reponse);

            return new JsonResponse($reponse);
        }

        if($_POST){
            $resultat = [];
            foreach($fiches as $fiche){
                $criteres = end($fiche->CRITERES);
                $attributes = end($criteres)->attributes();
                foreach($attributes as $key => $critere){
                    if(in_array($critere, $_POST)){
                        $resultat[] = $fiche;
                        $donnees[] = [
                            'numero' => (string)$fiche->PRODUIT,
                            'lat' => str_replace(',', '.', $fiche->LATITUDE),
                            'lng' => str_replace(',', '.', $fiche->LONGITUDE),
                            'titre' => (string)$fiche->NOM,
                            'image' => (string)$fiche->CRITERES->Crit[0],
                            'lien' => str_replace(' ', '-', $fiche->NOM)
                        ];
                        break;
                    }
                }
            }
            $fiches = $resultat;
        }

        return $this->render('recherche.html.twig', array('fiches' => $fiches, 'recherche' => $_POST, 'pageRecherche' => true, 'donnees' => $donnees));
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

        $fiche = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]")[0];

        return $this->render('Blocs/LEI/fiche.html.twig', array('fiche'=>$fiche));
    }
}