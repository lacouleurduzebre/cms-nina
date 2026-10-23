<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-21
 * Time: 14:09
 */

namespace App\Twig\Front;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class Droits extends AbstractExtension
{
    private $droits;

    public function __construct(\App\Service\Droits $droits){
        $this->droits = $droits;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('checkDroit', array($this, 'checkDroit'))
        );
    }

    public function checkDroit($droit){
        return $this->droits->checkDroit($droit);
    }
}