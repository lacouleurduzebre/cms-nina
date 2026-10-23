<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 10:19
 */

namespace App\Blocs\Categorie;


use App\Entity\Page;
use App\Service\Pagination;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategorieTwig extends AbstractExtension
{
    private $doctrine;
    private $pagination;

    public function __construct(ManagerRegistry $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('pagesDeLaCategorie', array($this, 'pagesDeLaCategorie')),
        );
    }

    public function pagesDeLaCategorie($langue, $parametres)
    {
        $categorie = $parametres['categorie'];

        $repoPage = $this->doctrine->getRepository(Page::class);

        if(array_key_exists('tri', $parametres) && $parametres['tri'] == 'alpha'){
            $pages = $repoPage->pagesPublieesCategorie($categorie, $langue, null, 'alpha');
        }else{
            $pages = $repoPage->pagesPublieesCategorie($categorie, $langue);
        }

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->pagination->getPagination($pages, $parametres, $page);
    }
}