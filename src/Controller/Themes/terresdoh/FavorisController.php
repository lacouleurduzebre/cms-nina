<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 04/09/2018
 * Time: 14:44
 */

namespace App\Controller\Themes\terresdoh;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    /**
     * @Route("/{_locale}/favoris", name="listeFavorisLEI")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listeAction(){
        if(isset($_COOKIE['favoris'])){
            $numerosFiches = json_decode($_COOKIE['favoris'], true);
            $xml = simplexml_load_file('https://apps.tourisme-alsace.info/batchs/LIENS_PERMANENTS/2002206000029_Batch_siteweb_terres_oh.xml');

            $fiches = [];
            foreach($numerosFiches as $numeroFiche){
                $fiche = $xml->xpath("//Resultat/sit_liste[PRODUIT = $numeroFiche]");
                $fiches[] = $fiche[0];
            }
        }else{
            $fiches = null;
        }
        return $this->render('listeFavoris.html.twig', array('fiches' => $fiches));
    }
}