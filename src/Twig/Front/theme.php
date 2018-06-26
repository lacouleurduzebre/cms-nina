<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 26/06/2018
 * Time: 10:30
 */

namespace App\Twig\Front;


use Symfony\Bridge\Doctrine\RegistryInterface;

class theme extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getGlobals()
    {
        $em = $this->doctrine->getRepository(\App\Entity\Configuration::class);
        $config = $em->findOneBy(array('id'=>'1'));
        $theme = $config->getTheme();

        if(!$theme){
            $theme = 'nina';
        }

        return array('theme'=>$theme);
    }
}