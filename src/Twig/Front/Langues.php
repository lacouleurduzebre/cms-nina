<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2017
 * Time: 10:38
 */

namespace App\Twig\Front;


use App\Entity\Langue;
use Symfony\Bridge\Doctrine\RegistryInterface;

class Langues extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getGlobals()
    {
        $em = $this->doctrine->getRepository(Langue::class);
        $langues = $em->findBy(array('active' => '1'));

        return array('langues'=>$langues);
    }
}