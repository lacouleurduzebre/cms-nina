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
    /**
     * @Route("/admin/menu/enregistrer", name="enregistrerMenu")
     * @param Request $request
     * @return bool|Response
     */
    public function enregistrerAction(Request $request){
        if($request->isXmlHttpRequest()){
            $arbo = $request->get('arbo');
            $repositoryMenu = $this->getDoctrine()->getRepository(Menu::class);
            $repositoryMenuPage = $this->getDoctrine()->getRepository(MenuPage::class);
            $repositoryPage = $this->getDoctrine()->getRepository(Page::class);
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
                    continue;
                }

                if($item['0'] == 0){// menuPage n'existe pas, on le crée
                    $menuPage = new MenuPage();
                }else{
                    $menuPage = $repositoryMenuPage->findOneBy(array('id'=>$item[0]));
                }

                $menu = $repositoryMenu->findOneBy(array('id'=>$item[4]));
                $page = $repositoryPage->findOneBy(array('id'=>$item[1]));
                $pageParent = $repositoryPage->findOneBy(array('id'=>$item[3]));

                $menuPage->setMenu($menu);
                $menuPage->setPosition($item[2]);
                $menuPage->setPage($page);
                $menuPage->setPageParent($pageParent);

                $em->persist($menuPage);
            };

            $em->flush();

            return new Response('OK');
        }
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

            $idMenuPage = $request->get('idMenuPage');
            $menuPage = $this->getDoctrine()->getRepository(MenuPage::class)->find($idMenuPage);

            $em->remove($menuPage);
            $em->flush();

            return new Response('OK');
        }
        return false;
    }
}