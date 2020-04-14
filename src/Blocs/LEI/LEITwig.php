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
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LEITwig extends AbstractExtension
{
    private $doctrine;
    private $pagination;
    private $request;
    private $cache;


    public function __construct(ManagerRegistry $doctrine, Pagination $pagination, RequestStack $requestStack, CacheInterface $cache)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
        $this->request = $requestStack->getCurrentRequest();
        $this->cache = $cache;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('listeLEI', array($this, 'listeLEI')),
            new TwigFunction('getPhotoPrincipale', array($this, 'getPhotoPrincipale')),
            new TwigFunction('getPictoLEI', array($this, 'getPictoLEI')),
            new TwigFunction('getCritere', array($this, 'getCritere')),
            new TwigFunction('getHoraires', array($this, 'getHoraires')),
        );
    }

    public function listeLEI($bloc)
    {
        $parametres = $bloc->getContenu();

        //Utilisation du cache si dispo
        //Langue
        $repoLangue = $this->doctrine->getRepository(\App\Entity\Langue::class);
        $locale = $this->request->getLocale();
        if($locale){
            $langue = $repoLangue->findOneBy(array('abreviation'=>$locale));
        }
        if(!$locale || !$langue){
            $langue = $repoLangue->findOneBy(array('defaut'=>1));
        }
        //Fin langue

        //Utilisation du cache si dispo
        $cleCache = 'LEI_'.$bloc->getId().'_'.$langue->getAbreviation();

        if($_ENV['APP_ENV'] == 'prod'){
            $flux = $this->cache->get($cleCache);
        }

        if(array_key_exists("bloc-".$bloc->getId()."--libtext", $_POST) || !isset($flux)){//Recherche par mot-clé ou fichier de cache absent
            //Utilisation du flux générique ou du flux spécifique
            if(array_key_exists('utiliserFluxSpecifique', $parametres) && isset($parametres['utiliserFluxSpecifique'][0]) && isset($parametres['flux'])){
                $urlFlux = $parametres['flux'];
            }else{
                $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
                $urlFlux = $configLEI['fluxGenerique'];
            }

            //Ajout de la clause et des autres paramètres
            if(array_key_exists('clause', $parametres)){
                $urlFlux .= '&clause='.$parametres['clause'];
            }
            if(array_key_exists('autresParametres', $parametres)){
                $urlFlux .= $parametres['autresParametres'];
            }

            //Création du fichier de cache
            $file_headers = @get_headers($urlFlux);
            if ($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
                $flux = file_get_contents($urlFlux);
                if ($_ENV['APP_ENV'] == 'prod') {
                    $this->cache->set($cleCache, $flux, 86400);
                }
            }

            //Recherche par mot-clé
            if(array_key_exists("bloc-".$bloc->getId()."--libtext", $_POST)){
                $urlFlux .= '&libtext='.$_POST["bloc-".$bloc->getId()."--libtext"];
                $xml = simplexml_load_file($urlFlux);
            }else{
                $xml = simplexml_load_string($flux);
            }
        }else{
            $xml = simplexml_load_string($flux);
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

    public function getCritere($fiche, $critere){
        $criteres = $fiche->CRITERES;

        if($criteres->xpath("Crit[@CLEF_CRITERE='".$critere."']")){
            return $criteres->xpath("Crit[@CLEF_CRITERE='".$critere."']")[0];
        }

        return false;
    }

    public function getHoraires($horaires){//HORAIRES.Horaire
        $semaine = [
            'lundi' => [],
            'mardi' => [],
            'mercredi' => [],
            'jeudi' => [],
            'vendredi' => [],
            'samedi' => [],
            'dimanche' => []
        ];

        foreach($horaires->HEURES->Heure as $heure){
            if($heure->LUNDI == 'O'){
                $semaine['lundi'][] = [
                    'debut' => $heure->HEURE_DEBUT,
                    'fin' => $heure->HEURE_FIN
                ];
            }
            if($heure->MARDI == 'O'){
                $semaine['mardi'][] = [
                    'debut' => $heure->HEURE_DEBUT,
                    'fin' => $heure->HEURE_FIN
                ];
            }
            if($heure->MERCREDI == 'O'){
                $semaine['mercredi'][] = [
                    'debut' => $heure->HEURE_DEBUT,
                    'fin' => $heure->HEURE_FIN
                ];
            }
            if($heure->JEUDI == 'O'){
                $semaine['jeudi'][] = [
                    'debut' => $heure->HEURE_DEBUT,
                    'fin' => $heure->HEURE_FIN
                ];
            }
            if($heure->VENDREDI == 'O'){
                $semaine['vendredi'][] = [
                    'debut' => $heure->HEURE_DEBUT,
                    'fin' => $heure->HEURE_FIN
                ];
            }
            if($heure->SAMEDI == 'O'){
                $semaine['samedi'][] = [
                    'debut' => $heure->HEURE_DEBUT,
                    'fin' => $heure->HEURE_FIN
                ];
            }
            if($heure->DIMANCHE == 'O'){
                $semaine['dimanche'][] = [
                    'debut' => $heure->HEURE_DEBUT,
                    'fin' => $heure->HEURE_FIN
                ];
            }
        }

        return $semaine;
    }
}