<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2020-03-27
 * Time: 15:34
 */

namespace App\Twig;


use Twig\Template;

abstract class Debug extends Template
{
    public function display(array $context, array $blocks = [])
    {
        $racine =  dirname(dirname(__DIR__));
        $cheminTPL = str_replace($racine, '', $this->getSourceContext()->getPath());
        echo '<!-- ' . $cheminTPL . ' -->';
        parent::display($context, $blocks);
        echo '<!-- FIN ' . $cheminTPL . ' -->';
    }
}