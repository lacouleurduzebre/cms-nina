<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 09/01/2018
 * Time: 10:34
 */

namespace App\Twig\Front;


use App\Service\Page;
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

    public function isPublie($page)
    {
        return Page::isPublie($page);
    }
}