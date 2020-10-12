<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 22/08/2017
 * Time: 13:35
 */

namespace App\Service;


use Doctrine\Persistence\ManagerRegistry;

class Configuration
{
    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
    }

    public function getConfig(){
        $repo = $this->doctrine->getRepository(\App\Entity\Configuration::class);
        $config = $repo->find(1);

        return $config;
    }
}