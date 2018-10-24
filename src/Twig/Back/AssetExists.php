<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 13:14
 */

namespace App\Twig\Back;


use Symfony\Bridge\Doctrine\RegistryInterface;

class AssetExists extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('assetExists', array($this, 'assetExists')),
        );
    }

    public function assetExists($asset)
    {
        return file_exists($asset);
    }
}