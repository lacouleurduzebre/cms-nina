<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Config
 *
 * @ORM\Entity(repositoryClass="App\Repository\ConfigurationRepository")
 */
class Configuration
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="text")
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="emailContact", type="string", length=255)
     */
    private $emailContact;

    /**
     * @var string
     *
     * @ORM\Column(name="emailMaintenance", type="string", length=255)
     */
    private $emailMaintenance;

    /**
     * @var string
     *
     * @ORM\Column(name="analytics", type="text", nullable=true)
     */
    private $analytics;

    /**
     * @var string
     *
     * @ORM\Column(name="editeur", type="string", length=255)
     */
    private $editeur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="boolean")
     */
    private $maintenance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $affichageCommentaires;

    /**
     * @ORM\Column(type="boolean")
     */
    private $affichageDatePublication;

    /**
     * @ORM\Column(type="boolean")
     */
    private $affichageAuteur;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $nbArticlesFluxRSS;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bandeauCookies;

    public function __construct()
    {
        $this->maintenance = false;
        $this->affichageCommentaires = true;
        $this->affichageDatePublication = true;
        $this->affichageAuteur = true;
        $this->nbArticlesFluxRSS = 20;
        $this->bandeauCookies = false;
    }

    public function __toString()
    {
        return "configuration";
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
     * Set logo
     *
     * @param string $logo
     *
     * @return Configuration
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Configuration
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set emailContact
     *
     * @param string $emailContact
     *
     * @return Configuration
     */
    public function setEmailContact($emailContact)
    {
        $this->emailContact = $emailContact;

        return $this;
    }

    /**
     * Get emailContact
     *
     * @return string
     */
    public function getEmailContact()
    {
        return $this->emailContact;
    }

    /**
     * Set emailMaintenance
     *
     * @param string $emailMaintenance
     *
     * @return Configuration
     */
    public function setEmailMaintenance($emailMaintenance)
    {
        $this->emailMaintenance = $emailMaintenance;

        return $this;
    }

    /**
     * Get emailMaintenance
     *
     * @return string
     */
    public function getEmailMaintenance()
    {
        return $this->emailMaintenance;
    }

    /**
     * Set analytics
     *
     * @param string $analytics
     *
     * @return Configuration
     */
    public function setAnalytics($analytics)
    {
        $this->analytics = $analytics;

        return $this;
    }

    /**
     * Get analytics
     *
     * @return string
     */
    public function getAnalytics()
    {
        return $this->analytics;
    }

    /**
     * Set editeur
     *
     * @param string $editeur
     *
     * @return Configuration
     */
    public function setEditeur($editeur)
    {
        $this->editeur = $editeur;

        return $this;
    }

    /**
     * Get editeur
     *
     * @return string
     */
    public function getEditeur()
    {
        return $this->editeur;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getMaintenance(): ?bool
    {
        return $this->maintenance;
    }

    public function setMaintenance(bool $maintenance): self
    {
        $this->maintenance = $maintenance;

        return $this;
    }

    public function getAffichageCommentaires(): ?bool
    {
        return $this->affichageCommentaires;
    }

    public function setAffichageCommentaires(bool $affichageCommentaires): self
    {
        $this->affichageCommentaires = $affichageCommentaires;

        return $this;
    }

    public function getAffichageDatePublication(): ?bool
    {
        return $this->affichageDatePublication;
    }

    public function setAffichageDatePublication(bool $affichageDatePublication): self
    {
        $this->affichageDatePublication = $affichageDatePublication;

        return $this;
    }

    public function getAffichageAuteur(): ?bool
    {
        return $this->affichageAuteur;
    }

    public function setAffichageAuteur(bool $affichageAuteur): self
    {
        $this->affichageAuteur = $affichageAuteur;

        return $this;
    }

    public function getNbArticlesFluxRSS(): ?int
    {
        return $this->nbArticlesFluxRSS;
    }

    public function setNbArticlesFluxRSS(?int $nbArticlesFluxRSS): self
    {
        $this->nbArticlesFluxRSS = $nbArticlesFluxRSS;

        return $this;
    }

    public function getBandeauCookies(): ?bool
    {
        return $this->bandeauCookies;
    }

    public function setBandeauCookies(bool $bandeauCookies): self
    {
        $this->bandeauCookies = $bandeauCookies;

        return $this;
    }
}
