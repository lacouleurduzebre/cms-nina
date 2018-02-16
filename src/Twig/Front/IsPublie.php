<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 09/01/2018
 * Time: 10:34
 */

namespace App\Twig\Front;


use Twig\Environment;

class IsPublie extends \Twig_Extension
{
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('isPublie', array($this, 'isPublie')),
        );
    }

    public function isPublie($machin)
    {
        $timestamp = new \DateTime();
        $date = $timestamp->format('Y-m-d H:i:s');

        if($machin->getDatePublication() < $date && $machin->getDateDepublication() > $date || $machin->getDateDepublication() == null && $machin->getActive() == 1 && $machin->getCorbeille() == 0){
            return true;
        }

        return false;
    }
}