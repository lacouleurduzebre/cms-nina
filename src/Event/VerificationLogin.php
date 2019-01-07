<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2017
 * Time: 10:28
 */

namespace App\Event;


use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class VerificationLogin implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em, RequestStack $request)
    {
        $this->em = $em;
        $this->request = $request;
    }

    public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => array(array('echecConnexion', 15)),
            SecurityEvents::INTERACTIVE_LOGIN => array(array('connexion', 15)),
        );
    }

    public function echecConnexion(AuthenticationFailureEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        $user = $this->em->getRepository(Utilisateur::class)->findOneByUsername($username);

        if($user){
            $user->setTentativesConnexion($user->getTentativesConnexion() + 1);
            $user->setDateDerniereTentativeConnexion(new \DateTime());

            $this->em->persist($user);
            $this->em->flush();
        }

        return;
    }

    public function connexion(InteractiveLoginEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        $user = $this->em->getRepository(Utilisateur::class)->findOneByUsername($username);

        if($user){
            $user->setTentativesConnexion(0);

            $this->em->persist($user);
            $this->em->flush();
        }

        return;
    }
}