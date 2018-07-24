<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 05/09/2017
 * Time: 10:55
 */

namespace App\Twig\Front;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class Zone extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('zone', array($this, 'getZone'), array('is_safe' => ['html'])),
        );
    }

    public function getZone($region = null, $id = null)
    {
        $repoZone = $this->doctrine->getRepository(\App\Entity\Zone::class);
        if($region){
            $zones = $repoZone->findBy(array('region' => $region));
            return $this->twig->render('front/zones.html.twig', array('zones'=>$zones));
        }else{
            $zone = $repoZone->find($id);
            return $this->twig->render('front/zones.html.twig', array('zone'=>$zone));
        }
    }
}