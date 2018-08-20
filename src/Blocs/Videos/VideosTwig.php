<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 17/07/2018
 * Time: 13:35
 */

namespace App\Blocs\Videos;


use App\Entity\Bloc;
use Symfony\Bridge\Doctrine\RegistryInterface;

class VideosTwig extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('listerToutesLesVideos', array($this, 'listerToutesLesVideos')),
        );
    }

    public function listerToutesLesVideos()
    {
        $repoBloc = $this->doctrine->getRepository(Bloc::class);
        $blocsVideos = $repoBloc->findBy(array("type" => "Video"));

        return array('blocsVideos' => $blocsVideos);
    }
}