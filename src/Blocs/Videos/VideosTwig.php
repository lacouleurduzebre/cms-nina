<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\Videos;


use App\Entity\Bloc;
use App\Service\Pagination;
use Doctrine\Persistence\ManagerRegistry;

class VideosTwig extends \Twig_Extension
{
    public function __construct(ManagerRegistry $doctrine, Pagination $pagination)
    {
        $this->doctrine = $doctrine;
        $this->pagination = $pagination;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listeVideos', array($this, 'listeVideos')),
        );
    }

    public function listeVideos($langue, $parametres)
    {
        $repoBloc = $this->doctrine->getRepository(Bloc::class);
        $blocsVideos = $repoBloc->videosPagesPubliees($langue);

        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->pagination->getPagination($blocsVideos, $parametres, $page);
    }
}