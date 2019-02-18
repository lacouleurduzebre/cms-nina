<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 05/09/2017
 * Time: 10:55
 */

namespace App\Twig\Front;

use App\Entity\Bloc;
use App\Entity\Langue;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Front extends \Twig_Extension
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
            new \Twig_SimpleFunction('regions', array($this, 'getRegions'), array('is_safe' => ['html'])),
            new \Twig_SimpleFunction('groupes', array($this, 'getGroupesBlocs'), array('is_safe' => ['html'])),
            new \Twig_SimpleFunction('groupe', array($this, 'getGroupeBlocs'), array('is_safe' => ['html'])),
            new \Twig_SimpleFunction('blocAnnexe', array($this, 'getBlocAnnexe'), array('is_safe' => ['html'])),
        );
    }

    public function getRegions($position){
        $repoRegion = $this->doctrine->getRepository(Region::class);

        $positionContenu = $repoRegion->findOneBy(array('identifiant' => 'contenu'))->getPosition();

        if($position == 'avant'){
            $regions = $repoRegion->getRegionsAvant($positionContenu);
        }else{
            $regions = $repoRegion->getRegionsApres($positionContenu);
        }

        $rendu = '';
        foreach($regions as $region){
            $tpl = 'front/regions/region-'.$region->getIdentifiant().'.html.twig';
            if($this->twig->getLoader()->exists($tpl)){
                $rendu .= $this->twig->render($tpl, array('region' => $region));
            }else{
                $rendu .= $this->twig->render('front/regions/region.html.twig', array('region' => $region));
            }
        }

        return $rendu;
    }

    public function getGroupesBlocs($idRegion)
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
        //Fin langue

        $repoGroupeBlocs = $this->doctrine->getRepository(\App\Entity\GroupeBlocs::class);
        $groupesBlocs = $repoGroupeBlocs->findBy(array('region' => $idRegion, 'langue' => $langue ), array('position' => 'ASC'));

        $rendu = '';
        foreach($groupesBlocs as $groupeBlocs){
            $tpl = 'front/groupes/groupe-'.$groupeBlocs->getIdentifiant().'.html.twig';
            if($this->twig->getLoader()->exists($tpl)){
                $rendu .= $this->twig->render($tpl, array('groupe' => $groupeBlocs));
            }else{
                $rendu .= $this->twig->render('front/groupes/groupe.html.twig', array('groupe' => $groupeBlocs));
            }
        }

        return $rendu;
    }

    public function getGroupeBlocs($idGroupeBlocs)
    {
        $repoGroupeBlocs = $this->doctrine->getRepository(\App\Entity\GroupeBlocs::class);
        $groupeBlocs = $repoGroupeBlocs->find($idGroupeBlocs);

        $rendu = '';
        $tpl = 'front/groupes/groupe-'.$groupeBlocs->getIdentifiant().'.html.twig';
        if($this->twig->getLoader()->exists($tpl)){
            $rendu .= $this->twig->render($tpl, array('groupe' => $groupeBlocs));
        }else{
            $rendu .= $this->twig->render('front/groupes/groupe.html.twig', array('groupe' => $groupeBlocs));
        }

        return $rendu;
    }

    public function getBlocAnnexe($page, $type, $complet = true){
        $repoBlocAnnexe = $this->doctrine->getRepository(\App\Entity\BlocAnnexe::class);
        $blocAnnexe = $repoBlocAnnexe->findOneBy(array('page' => $page, 'type' => $type));

        if($blocAnnexe){
            if($complet){
                return $this->twig->render('Blocs/'.$type.'/'.$type.'.html.twig', array('bloc' => $blocAnnexe));
            }else{
                return $this->twig->render('Blocs/'.$type.'/'.$type.'Brut.html.twig', array('bloc' => $blocAnnexe));
            }
        }

        return false;
    }
}