<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2017
 * Time: 10:38
 */

namespace App\Twig\Front;


use App\Entity\Langue;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LangueActive extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $doctrine;
    private $request;

    public function __construct(RegistryInterface $doctrine, RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getGlobals()
    {
        if($this->request){
            $locale = $this->request->getLocale();
            $repoLangue = $this->doctrine->getRepository(Langue::class);
            $langue = $repoLangue->findOneBy(array('abreviation'=>$locale));
            return array('langueActive'=>$langue);
        }else{
            return array('langueActive'=>null);
        }
    }
}