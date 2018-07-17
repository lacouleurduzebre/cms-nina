<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 11/07/2018
 * Time: 08:25
 */

namespace App\Event;


use App\Entity\MenuPage;
use App\Entity\Module;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PageAdmin implements EventSubscriberInterface
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.post_persist' => array('creerMenuPage'),
        );
    }

    public function creerMenuPage(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof Page)) {
            return;
        }

        $repoMenuPage = $this->doctrine->getRepository(MenuPage::class);

        $menuPage = $repoMenuPage->findOneBy(array('page'=>$entity));

        if($menuPage){
           return;
        }

        $menuPage = new MenuPage();
        $menuPage->setPage($entity)->setPosition(0);
        $event['em']->persist($menuPage);
        $event['em']->flush();

        return;
    }
}