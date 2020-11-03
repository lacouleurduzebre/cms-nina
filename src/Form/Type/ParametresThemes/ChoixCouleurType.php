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

class ChoixCouleurType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $configurationTheme = Yaml::parseFile('../themes/nina/config.yaml');
        $parametres = Yaml::parseFile('../themes/nina/parametres.yaml');

        $couleurs = [];
        foreach($configurationTheme['champs'] as $champ => $infos){
            if($infos['type'] == 'color'){
                if($parametres && key_exists($champ, $parametres)){//Paramètre modifié par l'utilisateur
                    $valeur =  $parametres[$champ];
                }else{//Paramètre par défaut
                    $valeur = $infos['defaut'];
                }

                $label = $infos['options']['label'];
                $couleurs[$label.'<span class="echantillonCouleur" style="background-color: '.$valeur.'"></span>'] = '$'.$champ;
            }
        }

        $couleurs['Noir <span class="echantillonCouleur" style="background-color: #000"></span>'] = '#000';
        $couleurs['Blanc <span class="echantillonCouleur" style="background-color: #FFF"></span>'] = '#FFF';
        $couleurs['Transparent <span class="echantillonCouleur"></span>'] = 'transparent';

        $resolver->setDefaults(array(
            'choices' => $couleurs,
            'expanded' => true
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}