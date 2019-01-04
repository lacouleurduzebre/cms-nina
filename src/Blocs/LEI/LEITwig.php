<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\LEI;


use App\Entity\Bloc;
use App\Service\Pagination;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class LEITwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listeLEI', array($this, 'listeLEI')),
        );
    }

    public function listeLEI($parametres)
    {
        $flux = $parametres['flux'];
        $limite = $parametres['limite'];
        $cle = $parametres['clef_moda'];
        if(isset($parametres['pagination'][0])){
            $pagination = $parametres['pagination'][0];
        }else{
            $pagination = null;
        };
        $resultatsParPage = isset($parametres['resultatsParPage']) ? $parametres['resultatsParPage'] : 9;
        
        $xml = simplexml_load_file($flux);
        $fiches = $xml->xpath("//Resultat/sit_liste");

        $resultats = [];

        //Limitation à la clé de modalité
        if(isset($cle)){
            $fichesTriees = [];

            foreach($fiches as $fiche){
                $criteres = $fiche->CRITERES->Crit;
                foreach($criteres as $critere){
                    $attribute = $critere->attributes()['CLEF_MODA'];
                    if($attribute == $cle){
                        $fichesTriees[] = $fiche;
                        break;
                    }
                }
            }

            $fiches =  $fichesTriees;
        }

        //Limitation du nombre de résultats
        if($limite != null){
            $fiches = array_splice($fiches, 0, $limite);
        }

        //Pagination
        $nbResultats = count($fiches);

        if($pagination == 1) {
            if(!isset($_GET['page'])){
                $fiches = array_splice($fiches, 0, $resultatsParPage);
            }else{
                $pageActuelle = $this->pagination->testPageActuelle($_GET['page'], $nbResultats, $resultatsParPage);
                $offset = ($pageActuelle - 1) * $resultatsParPage;
                $fiches = array_splice($fiches, $offset, $resultatsParPage);
            }
        }

        $resultats['fiches'] = $fiches;
        $resultats['nbResultats'] = $nbResultats;
        if($pagination == 1){
            if(!isset($pageActuelle)){
                $pageActuelle = 1;
            }
            $resultats['pagination'] = $this->pagination->renderPagination($nbResultats, $resultatsParPage, $pageActuelle);
        }else{
            $resultats['pagination'] = null;
        }

        return $resultats;
    }
}