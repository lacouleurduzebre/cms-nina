<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 26/02/2019
 * Time: 14:42
 */

namespace App\Controller\Back\Admin;


use App\Controller\Back\AdminController;
use App\Service\Droits;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigurationAdminController
 * @package App\Controller\Back\Admin
 * @Route("/admin")
 */
class ConfigurationAdminController extends AdminController
{
    private $droits;

    public function __construct(Droits $droits)
    {
        $this->droits = $droits;
    }

    protected function editConfigurationAction(){
        if(!$this->droits->checkDroit('config')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        return $this->editAction();
    }
}