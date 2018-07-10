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
 * @Vich\Uploadable
 */
class Utilisateur extends BaseUser
{
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
     * @Vich\UploadableField(mapping="imageProfil", fileNameProperty="imageProfil")
     * @var File
     */
    private $imageProfilFichier;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $maj;

    public function __construct()
    {
        parent::__construct();
        $this->pages = new ArrayCollection();
        $this->pagesModifiees = new ArrayCollection();
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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageProfilFichier(?File $imageProfil = null): void
    {
        $this->imageProfilFichier = $imageProfil;

        if (null !== $imageProfil) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->maj = new \DateTime('now');
        }
    }

    public function getimageProfilFichier(): ?File
    {
        return $this->imageProfilFichier;
    }

    /**
     * Set maj
     *
     * @param \DateTime $maj
     *
     * @return Utilisateur
     */
    public function setMaj($maj)
    {
        $this->maj = $maj;

        return $this;
    }

    /**
     * Get maj
     *
     * @return \DateTime
     */
    public function getMaj()
    {
        return $this->maj;
    }
}
