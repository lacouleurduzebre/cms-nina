<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\LEI;


use App\Entity\Bloc;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class LEITwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listeLEI', array($this, 'listeLEI')),
        );
    }

    public function listeLEI($idBloc, $flux, $limite = null)
    {
        $xml = simplexml_load_file($flux);
        $fiches = $xml->xpath("//Resultat/sit_liste");

        if(isset($limite)){
            $fiches = array_splice($fiches, 0, $limite);
        }

        return $this->twig->render('Blocs/LEI/liste.html.twig', array('fiches' => $fiches, 'idBloc' => $idBloc));
    }
}