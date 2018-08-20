<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 11/07/2018
 * Time: 08:25
 */

namespace App\Event;


use App\Entity\MenuPage;
use App\Entity\Bloc;
use App\Entity\Page;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PageAdmin implements EventSubscriberInterface
{
    public function __construct(RegistryInterface $doctrine, TokenStorageInterface $tokenStorage)
    {
        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.post_persist' => array('creerMenuPage'),
            'easy_admin.pre_persist' => array('auteurs', 10),
            'easy_admin.pre_update' => array('auteurs', 10),
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

    public function auteurs(GenericEvent $event){
        $entity = $event->getSubject();

        if (!($entity instanceof Page)) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        $entity->setAuteurDerniereModification($user);

        if(!$entity->getAuteur()){
            $entity->setAuteur($user);
        }
    }
}