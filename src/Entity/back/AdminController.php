<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 16/02/2018
 * Time: 10:01
 */

namespace App\Controller\Back;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends BaseAdminController
{
    /**
     * @Route("/admin", name="tableauDeBord")
     * @return string
     */
    public function tableauDeBord(){
        return 'Hello World';
    }
}