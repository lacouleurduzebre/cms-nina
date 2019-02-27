<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 10:19
 */

namespace App\Blocs\Categorie;


use App\Entity\Categorie;
use App\Entity\Page;
use App\Service\Pagination;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategorieTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pagesDeLaCategorie', array($this, 'pagesDeLaCategorie')),
        );
    }

    public function pagesDeLaCategorie($langue, $parametres)
    {
        $categorie = $parametres['categorie'];

        $repoPage = $this->doctrine->getRepository(Page::class);

        if($parametres['tri'] == 'alpha'){
            $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, null, 'alpha');
        }else{
            $pages = $repoPage->pagesPublieesCategorie($categorie, $langue);
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->pagination->getPagination($pages, $parametres, $page);
    }
}