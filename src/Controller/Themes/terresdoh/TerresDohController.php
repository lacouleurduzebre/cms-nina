<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 27/08/2018
 * Time: 09:00
 */

namespace App\Controller\Themes\terresdoh;


use Mpdf\Config\ConfigVariables;
use Mpdf\Mpdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

//Développement Terres d'Oh!
class TerresDohController extends AbstractController
{
    /**
     * @Route("/{_locale}/recherche", name="rechercheLEI")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function rechercheAction(Request $request){
        $fiches = simplexml_load_file('https://apps.tourisme-alsace.info/batchs/LIENS_PERMANENTS/2002206000029_Batch_siteweb_terres_oh.xml')->Resultat->children();
        $fichesFiltrees = null;

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
            $fichesFiltrees = $resultat;

            $reponse['template'] = $this->render('Blocs/LEI/liste.html.twig', array('fiches' => $fichesFiltrees))->getContent();
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
            $fichesFiltrees = $resultat;
        }

        return $this->render('recherche.html.twig', array('fiches' => $fichesFiltrees, 'recherche' => $_POST, 'pageRecherche' => true, 'donnees' => $donnees));
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

    /**
     * @Route("/{_locale}/favoris", name="listeFavorisLEI")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listeFavorisAction(Request $request){
        //Export PDF
        if(isset($_COOKIE['favoris'])){
            $fiches = $this->getFiches();
        }else{
            $fiches = null;
        }
        return $this->render('Favoris/listeFavoris.html.twig', array('fiches' => $fiches));
    }

    /**
     * @Route("/{_locale}/exportFavoris", name="exportListeFavorisLEI")
     * @Route("/{_locale}/exportFavoris/photos", name="exportListeFavorisLEIPhotos")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function exportListeFavorisAction(Request $request){
        $route = $request->get('_route');
        ($route == 'exportListeFavorisLEIPhotos') ? $photos = true : $photos = false;

        if(isset($_COOKIE['favoris'])){

            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $fiches = $this->getFiches();

            $header = file_get_contents('themes/terresdoh/templates/Favoris/exportListeFavorisHeader.html');
            $footer = file_get_contents('themes/terresdoh/templates/Favoris/exportListeFavorisFooter.html');

            $urlCarte = "https://maps.googleapis.com/maps/api/staticmap?size=670x850&format=jpeg&key=AIzaSyD0z61UOgKU7tqKo9_Hy7WaGxHc6-ovbxc&markers=";
            foreach($fiches as $fiche){
                $lat = round(str_replace(',', '.', $fiche->LATITUDE), 6);
                $lng = round(str_replace(',', '.', $fiche->LONGITUDE), 6);
                $urlCarte = $urlCarte.$lat.','.$lng.'|';
            }
            $carte = '<img src="'.$urlCarte.'">';

            $html = $this->render('Favoris/exportListeFavoris.html.twig', array('fiches' => $fiches, 'photos' => $photos))->getContent();

            $mpdf = new Mpdf([
                'tempDir' => '../var/temp/mpdf',
                'fontDir' => array_merge($fontDirs, [
                    '../public/themes/terresdoh/fonts',
                ]),
                'default_font' => 'Montserrat',
            ]);
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->useSubstitutions = false;
            $mpdf->simpleTables = true;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);
            $mpdf->WriteHTML($carte);
            $mpdf->AddPage();
            $mpdf->WriteHTML($html);
            $mpdf->Output('carnet-de-voyage.pdf', 'I');

            return new Response($html);
        }else{
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }
    }

    private function getFiches(){
        $numerosFiches = json_decode($_COOKIE['favoris'], true);
        $xml = simplexml_load_file('https://apps.tourisme-alsace.info/batchs/LIENS_PERMANENTS/2002206000029_Batch_siteweb_terres_oh.xml');

        $fiches = [];
        foreach($numerosFiches as $numeroFiche){
            $fiche = $xml->xpath("//Resultat/sit_liste[PRODUIT = $numeroFiche]");
            $fiches[] = $fiche[0];
        }

        return $fiches;
    }
}