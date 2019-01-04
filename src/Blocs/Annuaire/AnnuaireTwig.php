<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\Annuaire;


use App\Entity\Utilisateur;
use App\Service\Pagination;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;

class AnnuaireTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listeUtilisateurs', array($this, 'listeUtilisateurs')),
        );
    }

    public function listeUtilisateurs($parametres)
    {
        $repoUtilisateur = $this->doctrine->getRepository(Utilisateur::class);
        $utilisateurs = $repoUtilisateur->findAll();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->pagination->getPagination($utilisateurs, $parametres, $page);
    }
}