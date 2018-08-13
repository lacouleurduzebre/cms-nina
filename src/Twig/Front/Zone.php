<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 05/09/2017
 * Time: 10:55
 */

namespace App\Twig\Front;

use App\Entity\Langue;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Zone extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig, RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('zone', array($this, 'getZone'), array('is_safe' => ['html'])),
        );
    }

    public function getZone($region = null, $id = null)
    {
        //Langue
        $locale = $this->request->getLocale();
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $langue = $repoLangue->findBy(array('abreviation'=>$locale));

        //Zones
        $repoZone = $this->doctrine->getRepository(\App\Entity\Zone::class);

        if($region){
            $zones = $repoZone->findBy(array('region' => $region, 'langue' => $langue), array('position' => 'ASC'));
        }else{
            $zones = [];
            $zones[] = $repoZone->find($id);
        }

        return $this->twig->render('front/zones.html.twig', array('zones'=>$zones));
    }
}