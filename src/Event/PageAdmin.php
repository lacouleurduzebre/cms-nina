<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 11/07/2018
 * Time: 08:25
 */

namespace App\Event;


use App\Entity\MenuPage;
use App\Entity\Page;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PageAdmin implements EventSubscriberInterface
{
    private $doctrine;
    private $tokenStorage;

    public function __construct(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage)
    {
        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            AfterEntityPersistedEvent::class => array('creerMenuPage'),
            BeforeEntityPersistedEvent::class => array('auteurs', 10),
            BeforeEntityUpdatedEvent::class => array('auteurs', 10),
        );
    }

    public function creerMenuPage(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

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
        $em = $this->doctrine->getManager();
        $em->persist($menuPage);
        $em->flush();

        return;
    }

    public function auteurs(AbstractLifecycleEvent $event){
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Page)) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        $entity->setAuteurDerniereModification($user);

        if(!$entity->getAuteur()){
            $entity->setAuteur($user);
        }

        return;
    }
}