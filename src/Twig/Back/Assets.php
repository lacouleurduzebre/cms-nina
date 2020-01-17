<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 13:14
 */

namespace App\Twig\Back;


use Symfony\Bridge\Doctrine\RegistryInterface;

class Assets extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('assetExists', array($this, 'assetExists')),
            new \Twig_SimpleFunction('getTaille', array($this, 'getTaille')),
            new \Twig_SimpleFunction('getSrcSet', array($this, 'getSrcSet')),
        );
    }

    public function assetExists($asset)
    {
        return file_exists($asset);
    }

    public function getTaille($asset, $taille)
    {
        $miniature = '/images/'.$taille.substr($asset, 8, strlen($asset));
        return file_exists(getcwd().$miniature) ? $miniature : $asset;
    }

    public function getSrcSet($asset)
    {
        $srcSet = '';

        $tailles = [
            'extra-large' => 2000,
            'large' => 1000,
            'medium' => 500,
            'small' => 200
        ];

        foreach($tailles as $taille => $valeur){
            $miniature = '/images/'.$taille.substr($asset, 8, strlen($asset));
            if(file_exists(getcwd().$miniature))
                $srcSet .= $miniature.' '.$valeur.'w, ';
        }

        $srcSet = substr($srcSet, 0, strlen($srcSet) - 2);

        return $srcSet;
    }
}