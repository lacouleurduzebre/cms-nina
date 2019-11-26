<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/07/2018
 * Time: 13:56
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class ChoixTypeBlocType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $types = [];
        $types[''] = '';
        $blocs = Yaml::parseFile('../src/Blocs/configBlocs.yaml');
        foreach($blocs as $bloc => $config){
            if($bloc !== 'BlocPartage' && file_exists('../src/Blocs/'.$bloc.'/infos.yaml') && $config['actif'] == 'oui'){
                $infos = Yaml::parseFile('../src/Blocs/'.$bloc.'/infos.yaml');
                if($infos['type'] == 'contenu'){
                    $types[$infos['nom']] = $bloc;
                }
            }
        }

        $resolver->setDefaults(array(
            'choices' => $types
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}