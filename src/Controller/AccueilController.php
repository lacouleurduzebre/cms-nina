<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 25/08/2017
 * Time: 13:59
 */

namespace App\Controller;


use App\Service\Droits;
use App\Service\Page;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     * @Route("/{_locale}", name="accueilLocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Page $spage, \App\Service\Langue $slangue, Droits $droits, ConfigManager $configManager, $_locale = null)
    {
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('accueil', $_locale);
        if($redirection){
            return $redirection;
        }

        //Marquer le body
        $accueil = 'accueil';

        $page = $spage->getPageActive();
        if(!($page instanceof \App\Entity\Page)){
            return $page;
        }

        /* Barre d'admin */
        if($droits->checkDroit('admin')){
            $configEA = $configManager->getEntityConfig('Page_Active');
            $champsEA = $configEA['form']['fields'] ?? [];
            $ongletsEdition = [];
            foreach($champsEA as $champ){
                if(key_exists('type', $champ) && $champ['type'] == 'easyadmin_tab'){
                    $ongletsEdition[] = $champ;
                }
            }
        }
        /* Fin barre d'admin */

        return $this->render('front/accueil.html.twig', ['page' => $page, 'accueil' => $accueil, 'ongletsEdition' => $ongletsEdition ?? false]);
    }
}