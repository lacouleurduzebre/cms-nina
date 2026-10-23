<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 13:14
 */

namespace App\Twig\Back;


use App\Entity\Page;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Active extends AbstractExtension
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('pageActive', array($this, 'pageActive')),
        );
    }

    public function pageActive($idPage)
    {
        $repoPage = $this->doctrine->getRepository(Page::class);
        $page = $repoPage->find($idPage);

        return $page->getActive();
    }
}