<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 21/08/2017
 * Time: 16:27
 */

namespace App\Twig\Front;


use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Yaml\Yaml;

class ParametresTheme extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getGlobals()
    {
        //Thème actif
        $em = $this->doctrine->getRepository(\App\Entity\Configuration::class);
        $config = $em->findOneBy(array('id'=>'1'));
        $theme = $config->getTheme();

        //Paramètres
        $parametres = null;

        $fichierDefaut = Yaml::parseFile('../themes/'.$theme.'/config.yaml');

        if(key_exists('champs', $fichierDefaut)){
            $champs = $fichierDefaut['champs'];

            $nomFichierParametres = '../themes/'.$theme.'/parametres.yaml';
            if(!file_exists($nomFichierParametres)){
                $fichiersParametres = fopen($nomFichierParametres, "w");
                fclose($fichiersParametres);
            }
            $parametres = Yaml::parseFile($nomFichierParametres);

            foreach($champs as $champ => $infos){
                if($parametres && key_exists($theme, $parametres)){//Paramètre modifié par l'utilisateur
                    $data = $parametres[$theme];
                }else{//Paramètre par défaut
                    $data = $infos['defaut'];
                }

                $parametres[$champ] = $data;
            }
        }

        return array('configTheme' => $parametres);
    }
}