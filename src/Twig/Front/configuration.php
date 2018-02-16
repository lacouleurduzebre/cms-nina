<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 21/08/2017
 * Time: 16:27
 */

namespace App\Twig\Front;


use Symfony\Bridge\Doctrine\RegistryInterface;

class configuration extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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

        return array('config'=>$config);
    }
}