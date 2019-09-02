<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-21
 * Time: 14:09
 */

namespace App\Twig\Front;


class Droits extends \Twig_Extension
{
    private $droits;

    public function __construct(\App\Service\Droits $droits){
        $this->droits = $droits;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('checkDroit', array($this, 'checkDroit'))
        );
    }

    public function checkDroit($droit){
        return $this->droits->checkDroit($droit);
    }
}