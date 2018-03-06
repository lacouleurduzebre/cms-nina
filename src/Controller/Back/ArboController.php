<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 06/12/2017
 * Time: 10:18
 */

namespace App\Controller\Back;


use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArboController extends Controller
{
    /**
     * @Route("/admin/arbo/enregistrer", name="enregistrerArbo")
     * @param Request $request
     * @return bool|Response
     */
    public function enregistrerAction(Request $request){
        if($request->isXmlHttpRequest()){
            $arbo = $request->get('arbo');
            $repository = $this->getDoctrine()->getRepository(Page::class);
            $em = $this->getDoctrine()->getManager();

            foreach($arbo as $item){
                $page = $repository->findOneBy(array('id'=>$item[0]));
                $pageParent = $repository->findOneBy(array('id'=>$item[2]));
                $page->setPosition($item[1]);
                $page->setPageParent($pageParent);
                $em->persist($page);
            }

            $em->flush();

            return new Response('OK');
        }
        return false;
    }
}