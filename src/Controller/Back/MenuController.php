<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 06/12/2017
 * Time: 10:18
 */

namespace App\Controller\Back;


use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends Controller
{
    public function requetes($item, $menuPage){
        $repositoryMenu = $this->getDoctrine()->getRepository(Menu::class);
        $repositoryPage = $this->getDoctrine()->getRepository(Page::class);

        $menu = $repositoryMenu->findOneBy(array('id'=>$item[4]));
        $page = $repositoryPage->findOneBy(array('id'=>$item[1]));
        $pageParent = $repositoryPage->findOneBy(array('id'=>$item[3]));

        $menuPage->setMenu($menu);
        $menuPage->setPosition($item[2]);
        $menuPage->setPage($page);
        $menuPage->setPageParent($pageParent);
    }

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
                if($item[0] == 0 && $item[4] == 0){// Si menuPage n'existe pas et que la page est mise dans les orphelins on ne fait rien
                    continue;
                }

                if($item['4'] == 0){// Si menuPage existe mais que la page est mise dans les orphelins, on le supprime
                    $menuPage = $repositoryMenuPage->findOneBy(array('id'=>$item[0]));
                    $em->remove($menuPage);
                    $data = $item['1']."*".'0';//Retour de l'id menuPage
                    continue;
                }

                if($item['0'] == 0){// menuPage n'existe pas, on le crée
                    $menuPage = new MenuPage();

                    $this->requetes($item, $menuPage);

                    $em->persist($menuPage);
                    $em->flush();
                    $em->refresh($menuPage);

                    $idMenupage = $menuPage->getId();
                    $data = $item['1']."*".$idMenupage;
                }else{
                    $menuPage = $repositoryMenuPage->findOneBy(array('id'=>$item[0]));

                    $this->requetes($item, $menuPage);

                    $em->persist($menuPage);
                }
            };

            $em->flush();

            if(isset($data)){
                return new Response($data);
            }else{
                return new Response('vide');
            }
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

            /* Recherche du menuPage à supprimer */
            $idMenuPage = $request->get('idMenuPage');
            $menuPage = $emMenuPage->find($idMenuPage);
            $page = $menuPage->getPage();

            /* Suppression menuPage */
            $em->remove($menuPage);
            $em->flush();

            /* Page orpheline ? */
            $pagePasOrpheline = $emMenuPage->findBy(array('page' => $page));

            if($pagePasOrpheline){
                return new Response('pas orpheline');
            }else{
                return new Response('orpheline');
            }
        }
        return false;
    }
}