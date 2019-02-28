<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 21/08/2017
 * Time: 16:27
 */

namespace App\Twig\Front;


use App\Entity\Langue;
use App\Entity\SEO;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class pageActuelle extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine, RequestStack $request)
    {
        $this->doctrine = $doctrine;
        $this->request = $request;
    }

    public function getGlobals()
    {
        $currentRequest = $this->request->getCurrentRequest();
        $url = $currentRequest->attributes->get('url');

        if($url){
            $repoSEO = $this->doctrine->getRepository(SEO::class);
            $SEO = $repoSEO->findOneBy(array('url' => $url));
            $page = $SEO->getPage();
        }else{
            if($currentRequest->attributes->get('_route') == 'accueil'){
                $locale = $currentRequest->getLocale();
                $repoLangue = $this->doctrine->getRepository(Langue::class);
                $langue = $repoLangue->findOneBy(array('abreviation' => $locale));
                $page = $langue->getPageAccueil();
            }else{
                $page = null;
            }
        }

        return array('pageActuelle' => $page);
    }
}