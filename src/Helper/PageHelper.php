<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2021-01-13
 * Time: 09:31
 */

namespace App\Helper;


use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;

class PageHelper
{
    //Onglets de la barre d'admin
    public static function getOnglets(ConfigManager $configManager){
        $configEA = $configManager->getEntityConfig('Page_Active');
        $champsEA1 = $configEA['form']['fields'] ?? [];
        $champsEA2 = $configEA['edit']['fields'] ?? [];
        $champsEA = array_merge($champsEA1, $champsEA2);
        $ongletsEdition = [];
        foreach($champsEA as $champ){
            if(key_exists('type', $champ) && $champ['type'] == 'easyadmin_tab'){
                $ongletsEdition[] = $champ;
            }
        }
        return $ongletsEdition;
    }
}