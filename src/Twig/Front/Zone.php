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

    public function getZone($region)
    {
        $em = $this->doctrine->getRepository(\App\Entity\Zone::class);

        $zones = $em->findBy(array('region' => $region));

        return $this->twig->render('front/zone.html.twig', array('zones'=>$zones));
    }
}