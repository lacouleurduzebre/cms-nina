<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 21/08/2017
 * Time: 16:27
 */

namespace App\Twig\Front;


use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Region;
use App\Service\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;

class Globals extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine, Page $page, RequestStack $request)
    {
        $this->doctrine = $doctrine;
        $this->servicePage = $page;
        $this->request = $request->getCurrentRequest();
    }

    public function getGlobals()
    {
        if($this->doctrine->getConnection()->isConnected()){
            //Config
            $repoConfig = $this->doctrine->getRepository(Configuration::class);

            try {
                $repoConfig->find(1);
            } catch (\Exception $e) {
                return ['installeur' => true];
            }

            $repoRegion = $this->doctrine->getRepository(Region::class);
            $contenu = $repoRegion->findOneBy(array('identifiant' => 'contenu'));
            if($contenu){
                $installeur = false;
            }else{
                $installeur = true;
            }

            $config = $repoConfig->find(1);

            //Thème
            if($config){
                $theme = $config->getTheme();
            }

            if(!$config || !$theme){
                $theme = 'nina';
            }

            //Paramètres du thèmes
            $configTheme = null;

            $fichierDefaut = Yaml::parseFile('../themes/'.$theme.'/config.yaml');

            if($fichierDefaut){
                if(key_exists('champs', $fichierDefaut)){
                    $champs = $fichierDefaut['champs'];

                    $nomFichierParametres = '../themes/'.$theme.'/parametres.yaml';
                    if(!file_exists($nomFichierParametres)){
                        $fichiersParametres = fopen($nomFichierParametres, "w");
                        fclose($fichiersParametres);
                    }
                    $configTheme = Yaml::parseFile($nomFichierParametres);

                    foreach($champs as $champ => $infos){
                        if($configTheme && key_exists($theme, $configTheme)){//Paramètre modifié par l'utilisateur
                            $data = $configTheme[$theme];
                        }else{//Paramètre par défaut
                            $data = $infos['defaut'];
                        }
                        $configTheme[$champ] = $data;
                    }
                }
            }

            //Page active
            $page = $this->servicePage->getPageActive();

            //Langues
            $repoLangue = $this->doctrine->getRepository(Langue::class);
            $langues = $repoLangue->findBy(array('active' => '1'));

            //Langue active
            if($this->request){
                $locale = $this->request->getLocale();
                if($locale){
                    $langueActive = $repoLangue->findOneBy(array('abreviation'=>$locale));
                }
                if(!$langueActive){
                    $langueActive = $repoLangue->findOneBy(array('defaut'=>1));
                }
            }else{
                $langueActive = null;
            }

            return array(
                'config' => $config,
                'page' => $page,
                'langues' => $langues,
                'langueActive' => $langueActive,
                'theme' => $theme,
                'installeur' => $installeur
            );
        }else{
            return ['installeur' => true];
        }
    }
}