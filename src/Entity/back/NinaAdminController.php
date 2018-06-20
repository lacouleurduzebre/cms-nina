<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 16/02/2018
 * Time: 10:01
 */

namespace App\Entity\back;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\Routing\Annotation\Route;


class NinaAdminController extends BaseAdminController
{
    /**
     * @Route("/admin", name="tableauDeBord")
     * @return string
     */
    public function tableauDeBord(){
        return 'Hello World';
    }
}