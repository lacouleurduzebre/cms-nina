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

class groupeBlocs extends \Twig_Extension
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
            new \Twig_SimpleFunction('blocs', array($this, 'getGroupeBlocs'), array('is_safe' => ['html'])),
        );
    }

    public function getGroupeBlocs($region = null, $id = null)
    {
        //Langue
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $locale = $this->request->getLocale();
        if($locale){
            $langue = $repoLangue->findBy(array('abreviation'=>$locale));
        }
        if(!$locale || !$langue){
            $langue = $repoLangue->findBy(array('defaut'=>1));
        }

        //Zones
        $repoGroupeBlocs = $this->doctrine->getRepository(\App\Entity\GroupeBlocs::class);

        if($region){
            $blocs = [];
            $groupesBlocs = $repoGroupeBlocs->findBy(array('region' => $region, 'langue' => $langue));
            foreach($groupesBlocs as $groupeBlocs){
                $blocsDuGroupe = $groupeBlocs->getBlocs();
                foreach($blocsDuGroupe as $bloc){
                    $blocs[] = $bloc;
                }
            }
        }else{
            $blocs = [];
            $blocsDuGroupe = $repoGroupeBlocs->find($id)->getBlocs();
            foreach($blocsDuGroupe as $bloc){
                $blocs[] = $bloc;
            }
        }

        return $this->twig->render('front/blocs.html.twig', array('blocs'=>$blocs));
    }
}