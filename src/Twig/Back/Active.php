<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 13:14
 */

namespace App\Twig\Back;


use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;

class Active extends \Twig_Extension
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pageActive', array($this, 'pageActive')),
        );
    }

    public function pageActive($idPage)
    {
        $repoPage = $this->doctrine->getRepository(Page::class);
        $page = $repoPage->find($idPage);

        return $page->getActive();
    }
}