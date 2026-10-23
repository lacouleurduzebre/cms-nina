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
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IsPublie extends AbstractExtension
{
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('isPublie', array($this, 'isPublie')),
        );
    }

    public function isPublie($page)
    {
        return Page::isPublie($page);
    }
}