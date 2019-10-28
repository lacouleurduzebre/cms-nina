<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 26/02/2019
 * Time: 15:30
 */

namespace App\Controller\Back\Admin;


use App\Controller\Back\AdminController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegionAdminController
 * @package App\Controller\Back\Admin
 * @Route("/admin")
 */
class RegionAdminController extends AdminController
{
    //Ajout de la liste des blocs dans $parameters
    protected function newRegionAction(){
        return $this->newAction(true);
    }

    protected function editRegionAction(){
        return $this->editAction(true);
    }
}