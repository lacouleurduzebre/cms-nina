<?php

namespace App\Doctrine;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Champs canoniques et hachage du mot de passe avant chaque enregistrement d'un utilisateur
 * (remplace le listener de FOSUserBundle)
 *
 * preFlush est appelé avant le calcul des modifications : un changement du seul mot de passe en clair est donc enregistré
 */
#[AsEntityListener(event: Events::preFlush, entity: Utilisateur::class)]
class UtilisateurListener
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function preFlush(Utilisateur $utilisateur): void
    {
        $utilisateur->setUsernameCanonical(Utilisateur::canonicaliser($utilisateur->getUsername()));
        $utilisateur->setEmailCanonical(Utilisateur::canonicaliser($utilisateur->getEmail()));

        $motDePasse = $utilisateur->getPlainPassword();
        if (null !== $motDePasse && '' !== $motDePasse) {
            $utilisateur->setPassword($this->hasher->hashPassword($utilisateur, $motDePasse));
            $utilisateur->setPlainPassword(null);
        }
    }
}
