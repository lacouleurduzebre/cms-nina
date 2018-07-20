<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Modules\Annuaire;


use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class AnnuaireTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Environment $twig)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listerUtilisateurs', array($this, 'listerUtilisateurs')),
        );
    }

    public function listerUtilisateurs()
    {
        $repoUtilisateur = $this->doctrine->getRepository(Utilisateur::class);
        $utilisateurs = $repoUtilisateur->findAll();

        return $utilisateurs;
    }
}