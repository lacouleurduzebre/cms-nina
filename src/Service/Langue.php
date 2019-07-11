<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-07-11
 * Time: 14:22
 */

namespace App\Service;


use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class Langue
{
    public function __construct(RegistryInterface $doctrine, RouterInterface $router)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    public function redirectionLocale($route, $locale, $options = []){
        $repoLangue = $this->doctrine->getRepository(\App\Entity\Langue::class);
        $nbLangues = $repoLangue->nombreActives();

        if(isset($locale)){
            $langue = $repoLangue->findOneBy(array('abreviation' => $locale));
            if($nbLangues == 1 || !$langue){
                return new RedirectResponse($this->router->generate($route, $options), 301);
            }
        }elseif(!isset($locale) && $nbLangues > 1){
            $langueDefaut = $repoLangue->findOneBy(array('defaut' => 1))->getAbreviation();
            $options['_locale'] = $langueDefaut;
            return new RedirectResponse($this->router->generate($route.'Locale', $options), 301);
        }

        return false;
    }
}