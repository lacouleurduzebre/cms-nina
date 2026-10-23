<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 13:14
 */

namespace App\Twig\Back;


use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Assets extends AbstractExtension
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('assetExists', array($this, 'assetExists')),
            new TwigFunction('getTaille', array($this, 'getTaille')),
            new TwigFunction('getSrcSet', array($this, 'getSrcSet')),
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