<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2020-11-03
 * Time: 09:22
 */

namespace App\Form\Type\ParametresThemes;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class ChoixPoliceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $configurationTheme = Yaml::parseFile('../themes/nina/config.yaml');
        $parametres = Yaml::parseFile('../themes/nina/parametres.yaml');

        $policesDisponibles = [];
        foreach($configurationTheme['champs'] as $champ => $infos){
            if($infos['type'] == 'polices'){
                if($parametres && key_exists($champ, $parametres)){//Paramètre modifié par l'utilisateur
                    $polices =  $parametres[$champ];
                }else{//Paramètre par défaut
                    $polices = $infos['defaut'];
                }
                break;
            }
        }

        foreach($polices as $cle => $police){
            $policesDisponibles[$police] = $police;
        }

        $resolver->setDefaults(array(
            'choices' => $policesDisponibles
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}