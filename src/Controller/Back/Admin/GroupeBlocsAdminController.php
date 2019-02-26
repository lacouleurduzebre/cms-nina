<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 26/02/2019
 * Time: 15:30
 */

namespace App\Controller\Back\Admin;


use App\Controller\Back\AdminController;

class GroupeBlocsAdminController extends AdminController
{
    protected function newGroupeBlocsAction(){
        return $this->newAvecListeBlocs();
    }

    protected function editGroupeBlocsAction(){
        return $this->editAvecListeBlocs();
    }
}