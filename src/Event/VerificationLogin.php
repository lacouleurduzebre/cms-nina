<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2017
 * Time: 10:28
 */

namespace App\Event;


use App\Entity\Configuration;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Twig\Environment;

class VerificationLogin implements EventSubscriberInterface
{
    private $em;
    private $requestStack;
    private $mailer;
    private $twig;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, MailerInterface $mailer, Environment $twig)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            LoginFailureEvent::class => array(array('echecConnexion', 15)),
            SecurityEvents::INTERACTIVE_LOGIN => array(array('connexion', 15)),
        );
    }

    public function echecConnexion(LoginFailureEvent $event)
    {
        $passport = $event->getPassport();
        if($passport && $passport->hasBadge(UserBadge::class)){
            $identifiant = $passport->getBadge(UserBadge::class)->getUserIdentifier();
        }else{
            $identifiant = $event->getRequest()->request->get('_username');
        }
        $flash = $this->requestStack->getSession()->getFlashBag();
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
                $flash->add('error', 'Identifiants invalides. Essais restants : '. $tentativesRestantes);
            }elseif($tentativesRestantes == 0){
                $flash->add('error', 'Identifiants invalides. Votre compte a été suspendu pour 10min');

                //Mail d'avertissement
                $config = $this->em->getRepository(Configuration::class)->find(1);
                $expediteur = $config->getEmailMaintenance();
                $ip = $event->getRequest()->getClientIp();
                $mailUtilisateur = $user->getEmail();

                $mail = (new Email())
                    ->subject('Blocage du compte '.$mailUtilisateur.' sur le site '.$config->getNom())
                    ->from($expediteur)
                    ->to($expediteur, $mailUtilisateur)
                    ->html($this->twig->render('back/mails/blocageCompte.html.twig', array('mail' => $mailUtilisateur, 'ip' => $ip)));

                $this->mailer->send($mail);
            }

            $this->em->persist($user);
            $this->em->flush();
        }

        return;
    }

    public function connexion(InteractiveLoginEvent $event)
    {
        $identifiant = $event->getAuthenticationToken()->getUserIdentifier();
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