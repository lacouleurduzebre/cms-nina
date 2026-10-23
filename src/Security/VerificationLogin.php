<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-22
 * Time: 15:08
 */

namespace App\Security;


use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VerificationLogin implements UserCheckerInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        $now = time();
        $derniereTentative = false;
        if($user->getDateDerniereTentativeConnexion()){
            $derniereTentative = $user->getDateDerniereTentativeConnexion()->getTimestamp();
        }

        if($user->getTentativesConnexion() > 2 && $derniereTentative && ($now - $derniereTentative < 600)){
            $tempsRestant = round(($derniereTentative + 600 - $now) / 60);
            $this->requestStack->getSession()->getFlashBag()->add('error', 'Votre compte est suspendu pour '.$tempsRestant.' minutes');
            throw new AuthenticationException();
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}