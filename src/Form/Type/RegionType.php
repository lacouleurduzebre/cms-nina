<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 29/06/2018
 * Time: 08:58
 */

namespace App\Form\Type;


use App\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class RegionType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $emConfig = $this->em->getRepository(Configuration::class);
        $theme = $emConfig->findOneBy(array('id' => 1))->getTheme();

        $config = Yaml::parseFile('../public/themes/'.$theme.'/config.yaml');
        $regions = $config['regions'];

        if(!$regions){
            $regions = Yaml::parseFile('../config/regions.yaml');
        }

        $regions = array_combine(array_values($regions), array_keys($regions));

        $resolver->setDefaults(array(
            'choices' => $regions
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}