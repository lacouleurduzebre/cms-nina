<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 09/03/2018
 * Time: 14:55
 */

namespace App\Service\Front;


use Symfony\Bridge\Doctrine\RegistryInterface;

class ContenuModule
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getContenuModule($type, $idModule){
        $repo = $this->doctrine->getRepository('\App\Entity\Modules\Module'.$type);

        $module = $repo->findOneBy(array("id"=>$idModule));

        return $module;
    }
}