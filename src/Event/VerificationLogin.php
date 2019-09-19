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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class VerificationLogin implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em, RequestStack $request, FlashBagInterface $flash)
    {
        $this->em = $em;
        $this->request = $request;
        $this->flash = $flash;
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
        $identifiant = $event->getAuthenticationToken()->getUsername();
        $user = $this->em->getRepository(Utilisateur::class)->findOneByUsername($identifiant);

        if(!$user){
            $user = $this->em->getRepository(Utilisateur::class)->findOneByEmail($identifiant);
        }

        if($user){
            $now = time();

            $derniereTentative = false;
            if($user->getDateDerniereTentativeConnexion()){
                $derniereTentative = $user->getDateDerniereTentativeConnexion()->getTimestamp();
            }
            if(!$derniereTentative || $now - $derniereTentative > 600){
                $user->setDateDerniereTentativeConnexion(new \DateTime());
                $user->setTentativesConnexion(1);
            }else{
                $user->setTentativesConnexion($user->getTentativesConnexion() + 1);
            }

            $tentativesRestantes = 3 - $user->getTentativesConnexion();

            if($tentativesRestantes > 0){
                $this->flash->add('error', 'Identifiants invalides. Essais restants : '. $tentativesRestantes);
            }elseif($tentativesRestantes == 0){
                $this->flash->add('error', 'Identifiants invalides. Votre compte a été suspendu pour 10min');
            }

            $this->em->persist($user);
            $this->em->flush();
        }

        return;
    }

    public function connexion(InteractiveLoginEvent $event)
    {
        $identifiant = $event->getAuthenticationToken()->getUsername();
        $user = $this->em->getRepository(Utilisateur::class)->findOneByUsername($identifiant);

        if(!$user){
            $user = $this->em->getRepository(Utilisateur::class)->findOneByEmail($identifiant);
        }

        if($user){
            $user->setTentativesConnexion(0);
            $user->setDateDerniereTentativeConnexion(null);

            $this->em->persist($user);
            $this->em->flush();
        }

        return;
    }
}