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

            foreach($arbo as $item){
                $menu = $repositoryMenu->findOneBy(array('id'=>$item[4]));
                $menuPage = $repositoryMenuPage->findOneBy(array('id'=>$item[0]));
                $page = $repositoryPage->findOneBy(array('id'=>$item[1]));
                $pageParent = $repositoryPage->findOneBy(array('id'=>$item[3]));

                $menuPage->setMenu($menu);
                $menuPage->setPosition($item[2]);
                $menuPage->setPage($page);
                $menuPage->setPageParent($pageParent);

                $em->persist($menuPage);
            }

            $em->flush();

            return new Response('OK');
        }
        return false;
    }
}