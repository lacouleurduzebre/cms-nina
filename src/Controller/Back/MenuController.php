<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 06/12/2017
 * Time: 10:18
 */

namespace App\Controller\Back;


use App\Entity\Langue;
use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\SEO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends Controller
{
    /**
     * @Route("/admin/menu/enregistrer", name="enregistrerMenu")
     * @param Request $request
     * @return bool|Response
     */
    public function enregistrerAction(Request $request){
        if($request->isXmlHttpRequest()){
            $arbo = $request->get('arbo');
            $repositoryMenuPage = $this->getDoctrine()->getRepository(MenuPage::class);
            $em = $this->getDoctrine()->getManager();

            //$item[0] menuPage
            //$item[1] page
            //$item[2] position
            //$item[3] pageParent
            //$item[4] menu
            foreach($arbo as $item){
                $menuPage = $repositoryMenuPage->find($item[0]);

                $repositoryMenu = $this->getDoctrine()->getRepository(Menu::class);
                $repositoryPage = $this->getDoctrine()->getRepository(Page::class);

                if($item[4] == 0){
                    $menuPage->setMenu(null);
                }else{
                    $menu = $repositoryMenu->findOneBy(array('id'=>$item[4]));
                    $menuPage->setMenu($menu);
                }

                $page = $repositoryPage->findOneBy(array('id'=>$item[1]));
                $pageParent = $repositoryPage->findOneBy(array('id'=>$item[3]));

                $menuPage->setPosition($item[2]);
                $menuPage->setPage($page);
                $menuPage->setPageParent($pageParent);

                $em->persist($menuPage);
            };

            $em->flush();

            return new Response('ok');
        };

        return false;
    }

    /**
     * @Route("/admin/menu/retirer", name="retirerDuMenu")
     * @param Request $request
     * @return bool|Response
     */
    public function retirerDuMenuAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $emMenuPage = $this->getDoctrine()->getRepository(MenuPage::class);

            //Recherche du menuPage à modifier
            $idMenuPage = $request->get('idMenuPage');
            $menuPage = $emMenuPage->find($idMenuPage);

            //Page orpheline ?
            $page = $menuPage->getPage();
            $menuPages = $emMenuPage->findBy(array('page' => $page));
            if(sizeof($menuPages) > 1){//Non -> on supprime ce menuPage
                $em->remove($menuPage);
                $em->flush();
                return new Response('pas orpheline');
            }else{//Oui -> on modifie menuPage
                $menuPage->setMenu(null);
                $em->persist($menuPage);
                $em->flush();
                return new Response('orpheline');
            }
        };

        return false;
    }

    /**
     * @Route("/admin/menu/ajouter", name="ajouterPageEnfant")
     * @param Request $request
     * @return bool|Response
     */
    public function ajouterPageEnfantAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $repoPage = $em->getRepository(Page::class);
            $repoSEO = $em->getRepository(SEO::class);
            $repoMenu = $em->getRepository(Menu::class);
            $repoLangue = $em->getRepository(Langue::class);

            //Création page
            $page = new Page();
            $titre = 'Page sans titre';

            $url = 'page-sans-titre';
            while($repoSEO->findOneBy(array('url' => $url))){
                $url = $url.'-copie';
            }

            $page->setTitre($titre);
            $SEO = new SEO();
            $SEO->setMetaTitre($titre)->setUrl($url);
            $page->setSeo($SEO);

            $locale = $request->getLocale();
            $langue = $repoLangue->findOneBy(array('abreviation' => $locale));
            $page->setLangue($langue);

            $em->persist($page);
            $em->persist($SEO);
            $em->flush();

            $idPage = $page->getId();

            //Création menuPage
            $menuPage = new MenuPage();

            $idPageParent = $request->get('idPageParent');
            $pageParent = $repoPage->find($idPageParent);
            $idMenu = $request->get('idMenu');
            $menu = $repoMenu->find($idMenu);

            $menuPage->setPosition(0)->setPage($page)->setPageParent($pageParent)->setMenu($menu);

            $em->persist($menuPage);
            $em->flush();

            $idMenuPage = $menuPage->getId();

            $data = $idPage.'*'.$idMenuPage;
            return new Response($data);
        };

        return false;
    }

    /**
     * @Route("/admin/menu/alias", name="creerAlias")
     * @param Request $request
     * @return bool|Response
     */
    public function creerAlias(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $repoMenu = $em->getRepository(Menu::class);
            $repoPage = $em->getRepository(Page::class);
            $repoMenuPage = $em->getRepository(MenuPage::class);

            //Page copiée à partir des pages orphelines ?
            $idAncienMenuComplet = $request->get('idAncienMenuComplet');
            if($idAncienMenuComplet == 'menu-0'){//Si oui on modifie menuPage
                $idAnciendMenuPage = $request->get('idAnciendMenuPage');
                $menuPage = $repoMenuPage->find($idAnciendMenuPage);
                $menuPage->setMenu(null);
            }else{//Si non on créé un nouveau menuPage
                $menuPage = new MenuPage();

                $idPage = $request->get('idPage');
                $page = $repoPage->find($idPage);

                $idMenu = $request->get('idMenu');
                $menu = $repoMenu->find($idMenu);

                $menuPage->setPosition(0)->setPage($page)->setPageParent(null)->setMenu($menu);
            }

            $em->persist($menuPage);

            $em->flush();

            $idMenuPage = $menuPage->getId();

            $data = $idMenuPage;
            return new Response($data);
        };

        return false;
    }

    /**
     * @Route("/admin/menu/voirPage", name="urlPage")
     * @param Request $request
     * @return bool|Response
     */
    public function urlPageAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $repoPage = $em->getRepository(Page::class);

            $idPage = $request->get('idPage');
            $page = $repoPage->find($idPage);
            $url = $page->getSeo()->getUrl();

            return new Response($url);
        };

        return false;
    }
}