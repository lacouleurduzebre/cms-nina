<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Config
 *
 * @ORM\Entity(repositoryClass="App\Repository\ConfigurationRepository")
 * @Vich\Uploadable
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
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="text")
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="logo", fileNameProperty="logo")
     * @var File
     */
    private $logoFichier;

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
     * @ORM\Column(name="emailNewsletter", type="string", length=255)
     */
    private $emailNewsletter;

    /**
     * @var string
     *
     * @ORM\Column(name="analytics", type="text")
     */
    private $analytics;

    /**
     * @var string
     *
     * @ORM\Column(name="editeur", type="string", length=255)
     */
    private $editeur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Langue")
     */
    private $langueDefaut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $maj;

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
     * Set url
     *
     * @param string $url
     *
     * @return Configuration
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setlogoFichier(?File $logo = null): void
    {
        $this->logoFichier = $logo;

        if (null !== $logo) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->maj = new \DateTime('now');
        }
    }

    public function getLogoFichier(): ?File
    {
        return $this->logoFichier;
    }

    /**
     * Set maj
     *
     * @param \DateTime $maj
     *
     * @return Configuration
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
     * Set emailNewsletter
     *
     * @param string $emailNewsletter
     *
     * @return Configuration
     */
    public function setEmailNewsletter($emailNewsletter)
    {
        $this->emailNewsletter = $emailNewsletter;

        return $this;
    }

    /**
     * Get emailNewsletter
     *
     * @return string
     */
    public function getEmailNewsletter()
    {
        return $this->emailNewsletter;
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

    /**
     * Set langueDefaut
     *
     * @param \App\Entity\Langue $langueDefaut
     *
     * @return Configuration
     */
    public function setLangueDefaut(\App\Entity\Langue $langueDefaut = null)
    {
        $this->langueDefaut = $langueDefaut;
    
        return $this;
    }

    /**
     * Get langueDefaut
     *
     * @return \App\Entity\Langue
     */
    public function getLangueDefaut()
    {
        return $this->langueDefaut;
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
}
