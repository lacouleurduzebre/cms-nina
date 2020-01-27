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
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AnnuaireTwig extends AbstractExtension
{
    public function __construct(ManagerRegistry $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('listeUtilisateurs', [$this, 'listeUtilisateurs']),
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