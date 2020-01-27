<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\LEI;


use App\Service\Pagination;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

class LEITwig extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listeLEI', array($this, 'listeLEI')),
            new \Twig_SimpleFunction('getPhotoPrincipale', array($this, 'getPhotoPrincipale')),
            new \Twig_SimpleFunction('getPictoLEI', array($this, 'getPictoLEI')),
        );
    }

    public function listeLEI($bloc)
    {
        $parametres = $bloc->getContenu();

        //Utilisation du cache si dispo
        $cache = '../src/Blocs/LEI/cache/cache'.$bloc->getId().'.xml';

        if(array_key_exists("bloc-".$bloc->getId()."--libtext", $_POST) || !file_exists($cache)){//Recherche par mot-clé ou fichier de cache absent
            //Utilisation du flux générique ou du flux spécifique
            if(array_key_exists('utiliserFluxSpecifique', $parametres) && isset($parametres['utiliserFluxSpecifique'][0])){
                $flux = $parametres['flux'];
            }else{
                $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
                $flux = $configLEI['fluxGenerique'];
            }

            //Ajout de la clause et des autres paramètres
            if(array_key_exists('clause', $parametres)){
                $flux .= '&clause='.$parametres['clause'];
            }
            if(array_key_exists('autresParametres', $parametres)){
                $flux .= $parametres['autresParametres'];
            }

            //Création du fichier de cache
            if(!file_exists($cache)){
                $file_headers = @get_headers($flux);
                if($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
                    copy($flux, $cache);
                }
            }

            //Recherche par mot-clé
            if(array_key_exists("bloc-".$bloc->getId()."--libtext", $_POST)){
                $flux .= '&libtext='.$_POST["bloc-".$bloc->getId()."--libtext"];
                $xml = simplexml_load_file($flux);
            }else{
                $xml = simplexml_load_file($cache);
            }
        }else{
            $xml = simplexml_load_file($cache);
        }

        $fiches = $xml->xpath("//Resultat/sit_liste");

        $cle = array_key_exists('clef_moda', $parametres) ? $parametres['clef_moda'] : false;

        //Limitation à la clé de modalité
        if($cle){
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

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->pagination->getPagination($fiches, $parametres, $page);
    }

    public function getPhotoPrincipale($criteres){
        $photo = [];

        if($criteres->xpath("Crit[@CLEF_CRITERE='736000294']")){
            $photo['photo'] = $criteres->xpath("Crit[@CLEF_CRITERE='736000294']")[0];//Lorraine
        }
        if($criteres->xpath("Crit[@CLEF_CRITERE='1900421']")){
            $photo['photo'] = 'https://'.$criteres->xpath("Crit[@CLEF_CRITERE='1900421']")[0];//Alsace
        }
        if($criteres->xpath("Crit[@CLEF_CRITERE='736001119']")){
            $photo['credits'] = $criteres->xpath("Crit[@CLEF_CRITERE='736001119']")[0];//Lorraine
        }
        if($criteres->xpath("Crit[@CLEF_CRITERE='1900480']")){
            $photo['credits'] = $criteres->xpath("Crit[@CLEF_CRITERE='1900480']")[0];//Alsace
        }

        return $photo;
    }

    public function getPictoLEI($critere, $modalite){
        $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
        $pictos = $configLEI['pictos'];

        foreach ($pictos as $key => $picto) {
            if (!isset($picto['critere']) || $picto['critere'] != $critere or !isset($picto['modalite']) || $picto['modalite'] != $modalite)
            {
                continue;
            }
            return $picto;
        }

        return false;
    }
}