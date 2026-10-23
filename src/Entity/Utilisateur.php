<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Utilisateur
 *
 * Reprend le modèle de FOSUserBundle (mêmes colonnes en base)
 */
#[ORM\Entity(repositoryClass: \App\Repository\UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    const ROLE_DEFAULT = 'ROLE_UTILISATEUR';
    const ROLE_SUPER_ADMIN = 'ROLE_ADMIN';

    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(name: 'username', type: 'string', length: 180)]
    protected $username;

    #[ORM\Column(name: 'username_canonical', type: 'string', length: 180, unique: true)]
    protected $usernameCanonical;

    #[ORM\Column(name: 'email', type: 'string', length: 180)]
    protected $email;

    #[ORM\Column(name: 'email_canonical', type: 'string', length: 180, unique: true)]
    protected $emailCanonical;

    #[ORM\Column(name: 'enabled', type: 'boolean')]
    protected $enabled = false;

    #[ORM\Column(name: 'salt', type: 'string', length: 255, nullable: true)]
    protected $salt;

    /**
     * Mot de passe haché
     */
    #[ORM\Column(name: 'password', type: 'string', length: 255)]
    protected $password;

    /**
     * Mot de passe en clair, haché à l'enregistrement (cf. App\Doctrine\UtilisateurListener)
     */
    protected $plainPassword;

    #[ORM\Column(name: 'last_login', type: 'datetime', nullable: true)]
    protected $lastLogin;

    /**
     * Jeton de réinitialisation du mot de passe
     */
    #[ORM\Column(name: 'confirmation_token', type: 'string', length: 180, unique: true, nullable: true)]
    protected $confirmationToken;

    #[ORM\Column(name: 'password_requested_at', type: 'datetime', nullable: true)]
    protected $passwordRequestedAt;

    #[ORM\Column(name: 'roles', type: 'array')]
    protected $roles = [];

    #[ORM\OneToMany(targetEntity: \App\Entity\Page::class, mappedBy: 'auteur')]
    protected $pages;

    #[ORM\OneToMany(targetEntity: \App\Entity\Page::class, mappedBy: 'auteurDerniereModification')]
    protected $pagesModifiees;

    /**
     * @var string
     */
    #[ORM\Column(name: 'imageProfil', type: 'text', nullable: true)]
    private $imageProfil;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Langue::class, cascade: ['persist', 'remove'])]
    private $langue;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $couleurBO;

    #[ORM\Column(type: 'array', nullable: true)]
    private $blocsTableauDeBord;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $tentativesConnexion;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateDerniereTentativeConnexion;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->pagesModifiees = new ArrayCollection();
        $this->blocsTableauDeBord = ['logVersion', 'referencement'];
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    //Même format que FOSUserBundle : les sessions ouvertes restent valides
    public function __serialize(): array
    {
        return [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ] = $data;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return $this->password === $user->getPassword()
            && $this->salt === $user->getSalt()
            && $this->username === $user->getUsername();
    }

    public static function canonicaliser(?string $chaine): ?string
    {
        return null === $chaine ? null : mb_convert_case($chaine, MB_CASE_LOWER, 'UTF-8');
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->passwordRequestedAt instanceof \DateTime
            && $this->passwordRequestedAt->getTimestamp() + $ttl > time();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        //Au moins un rôle
        $roles[] = static::ROLE_DEFAULT;

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add page
     *
     * @param \App\Entity\Page $page
     *
     * @return Utilisateur
     */
    public function addPage(\App\Entity\Page $page)
    {
        $this->pages[] = $page;
    
        return $this;
    }

    /**
     * Remove page
     *
     * @param \App\Entity\Page $page
     */
    public function removePage(\App\Entity\Page $page)
    {
        $this->pages->removeElement($page);
    }

    /**
     * Get pages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Add pagesModifiee
     *
     * @param \App\Entity\Page $pagesModifiee
     *
     * @return Utilisateur
     */
    public function addPagesModifiee(\App\Entity\Page $pagesModifiee)
    {
        $this->pagesModifiees[] = $pagesModifiee;
    
        return $this;
    }

    /**
     * Remove pagesModifiee
     *
     * @param \App\Entity\Page $pagesModifiee
     */
    public function removePagesModifiee(\App\Entity\Page $pagesModifiee)
    {
        $this->pagesModifiees->removeElement($pagesModifiee);
    }

    /**
     * Get pagesModifiees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagesModifiees()
    {
        return $this->pagesModifiees;
    }

    /**
     * Set imageProfil
     *
     * @param string $imageProfil
     *
     * @return Utilisateur
     */
    public function setImageProfil($imageProfil)
    {
        $this->imageProfil = $imageProfil;

        return $this;
    }

    /**
     * Get imageProfil
     *
     * @return string
     */
    public function getImageProfil()
    {
        return $this->imageProfil;
    }

    public function getLangue(): ?Langue
    {
        return $this->langue;
    }

    public function setLangue(?Langue $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function getCouleurBO(): ?string
    {
        return $this->couleurBO;
    }

    public function setCouleurBO(?string $couleurBO): self
    {
        $this->couleurBO = $couleurBO;

        return $this;
    }

    public function getBlocsTableauDeBord(): ?array
    {
        return $this->blocsTableauDeBord;
    }

    public function setBlocsTableauDeBord(?array $blocsTableauDeBord): self
    {
        $this->blocsTableauDeBord = $blocsTableauDeBord;

        return $this;
    }

    public function getTentativesConnexion(): ?int
    {
        return $this->tentativesConnexion;
    }

    public function setTentativesConnexion(?int $tentativesConnexion): self
    {
        $this->tentativesConnexion = $tentativesConnexion;

        return $this;
    }

    public function getDateDerniereTentativeConnexion(): ?\DateTimeInterface
    {
        return $this->dateDerniereTentativeConnexion;
    }

    public function setDateDerniereTentativeConnexion(?\DateTimeInterface $dateDerniereTentativeConnexion): self
    {
        $this->dateDerniereTentativeConnexion = $dateDerniereTentativeConnexion;

        return $this;
    }
}
