<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\MappedSuperclass;


/** @MappedSuperclass */
abstract class SEO
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
     * @Assert\NotBlank
     * @ORM\Column(name="url", type="string", length=190, unique=true)
     * @Assert\Length(
     *     maxMessage="Le champ ""URL"" ne doit pas dépasser les 190 caractères",
     *     max=190,
     * )
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="metaTitre", type="text", nullable=true)
     */
    private $metaTitre;

    /**
     * @var string
     *
     * @ORM\Column(name="metaDescription", type="text", nullable=true)
     */
    private $metaDescription;

    public function __toString()
    {
        return $this->getMetaTitre();
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
     * Set url
     *
     * @param string $url
     *
     * @return SEO
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
     * Set metaTitre
     *
     * @param string $metaTitre
     *
     * @return SEO
     */
    public function setMetaTitre($metaTitre)
    {
        $this->metaTitre = $metaTitre;

        return $this;
    }

    /**
     * Get metaTitre
     *
     * @return string
     */
    public function getMetaTitre()
    {
        return $this->metaTitre;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return SEO
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }
}
