<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 14/09/2018
 * Time: 15:21
 */

namespace App\Twig\Front;


use Twig\Extension\AbstractExtension;

class LienExterne extends AbstractExtension
{
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('lienExterne', array($this, 'lienExterne'))
        );
    }

    public function lienExterne($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        return $url;
    }
}