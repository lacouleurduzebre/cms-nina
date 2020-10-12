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
use App\Entity\Page;
use App\Entity\Region;
use Psr\SimpleCache\CacheInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\TwigFunction;

class Front extends \Twig_Extension
{
    private $doctrine;
    private $twig;
    private $request;
    private $router;
    private $cache;

    public function __construct(ManagerRegistry $doctrine, Environment $twig, RequestStack $requestStack, UrlGeneratorInterface $router, CacheInterface $cache)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->cache = $cache;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('regions', array($this, 'getRegions'), array('is_safe' => ['html'])),
            new \Twig_SimpleFunction('bloc', array($this, 'getBloc'), array('is_safe' => ['html'])),
            new \Twig_SimpleFunction('blocAnnexe', array($this, 'getBlocAnnexe'), array('is_safe' => ['html'])),
            new \Twig_SimpleFunction('page', array($this, 'getPage')),
            new \Twig_SimpleFunction('lienPage', array($this, 'getLienPage')),
            new TwigFunction('blocs', array($this, 'getBlocs')),
        );
    }

    public function getRegions($position){
        $repoRegion = $this->doctrine->getRepository(Region::class);

        $regionContenu = $repoRegion->findOneBy(array('identifiant' => 'contenu'));

        if($regionContenu){
            $positionContenu = $regionContenu->getPosition();

            if($position == 'avant'){
                $regions = $repoRegion->getRegionsAvant($positionContenu);
            }elseif($position == 'apres'){
                $regions = $repoRegion->getRegionsApres($positionContenu);
            }elseif($position == 'centre'){
                $regions = [$regionContenu];
            }else{
                return false;
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

        return false;
    }

    public function getBloc($idBloc)
    {
        $repoBloc = $this->doctrine->getRepository(Bloc::class);
        $bloc = $repoBloc->find($idBloc);

        return $bloc;
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

    public function getPage($id){
        $repoPage = $this->doctrine->getRepository(\App\Entity\Page::class);
        $page = $repoPage->find($id);

        return $page;
    }

    public function getLienPage($page){
        if(!$page instanceof Page){
            return false;
        }

        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $langues = $repoLangue->findBy(array('active' => '1'));

        $url = $page->getSeo()->getUrl();

        $langue = $page->getLangue();

        if($langue->getPageAccueil() === $page){//Page d'accueil
            if(count($langues) > 1){
                return $this->router->generate("accueilLocale", ['_locale' => $page->getLangue()->getAbreviation()], 0);
            }else{
                return $this->router->generate("accueil", [], 0);
            }
        }else{
            if(count($langues) > 1){
                return $this->router->generate("voirPageLocale", ['_locale' => $page->getLangue()->getAbreviation(), 'url' => $url], 0);
            }else{
                return $this->router->generate("voirPage", ['url' => $url], 0);
            }
        }
    }

    public function getBlocs($page){
        if(!$page instanceof Page){
            return false;
        }

        $cleCache = 'page_'.$page->getId().'_blocs';
        $blocs = $page->getBlocs();

        if($_ENV['APP_ENV'] == 'prod' && !$this->request->get('page')) {
            $tpl = $this->cache->get($cleCache);
        }

        if(!isset($tpl)){
            $tpl = $this->twig->render('front/blocs.html.twig', array('blocs' => $blocs));

            if($_ENV['APP_ENV'] == 'prod' && !$this->contientBlocLEIRechercheTexte($blocs) && !$this->request->get('page')){
                $this->cache->set($cleCache, $tpl, 86400);
            }
        }

        return $tpl;
    }

    private function contientBlocLEIRechercheTexte($blocs){
        foreach($blocs as $bloc){
            if($bloc->getType() == 'LEI'){
                $contenu = $bloc->getContenu();
                if(key_exists('recherche', $contenu) && $contenu['recherche'] == 'texte'){
                    return true;
                }
            }elseif($bloc->getType() == 'Section'){
                $blocsEnfants = $bloc->getBlocsEnfants();
                if($this->contientBlocLEIRechercheTexte($blocsEnfants)){
                    return true;
                };
            }
        }
        return false;
    }
}