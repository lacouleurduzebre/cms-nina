<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Utilisateur
 *
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 */
class Utilisateur extends BaseUser
{
    const ROLE_DEFAULT = 'ROLE_UTILISATEUR';
    const ROLE_SUPER_ADMIN = 'ROLE_ADMIN';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Page", mappedBy="auteur")
     */
    protected $pages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Page", mappedBy="auteurDerniereModification")
     */
    protected $pagesModifiees;

    /**
     * @var string
     *
     * @ORM\Column(name="imageProfil", type="text", nullable=true)
     */
    private $imageProfil;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Langue", cascade={"persist", "remove"})
     */
    private $langue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couleurBO;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $blocsTableauDeBord;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $tentativesConnexion;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDerniereTentativeConnexion;

    public function __construct()
    {
        parent::__construct();
        $this->pages = new ArrayCollection();
        $this->pagesModifiees = new ArrayCollection();
        $this->blocsTableauDeBord = ['logVersion', 'referencement'];
    }

    public function __toString()
    {
        return $this->getUsername();
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
