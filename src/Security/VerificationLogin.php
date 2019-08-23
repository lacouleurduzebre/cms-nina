<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-22
 * Time: 15:08
 */

namespace App\Security;


use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VerificationLogin implements UserCheckerInterface
{
    public function __construct(FlashBagInterface $flash)
    {
        $this->flash = $flash;
    }

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Utilisateur) {
            return false;
        }

        $now = time();
        $derniereTentative = false;
        if($user->getDateDerniereTentativeConnexion()){
            $derniereTentative = $user->getDateDerniereTentativeConnexion()->getTimestamp();
        }

        if($user->getTentativesConnexion() > 2 && $derniereTentative && ($now - $derniereTentative < 600)){
            $tempsRestant = round(($derniereTentative + 600 - $now) / 60);
            $this->flash->add('error', 'Votre compte est suspendu pour '.$tempsRestant.' minutes');
            throw new AuthenticationException();
        }

        return false;
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}