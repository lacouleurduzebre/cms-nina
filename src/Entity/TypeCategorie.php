<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TypeCategorie
 *
 * @ORM\Entity(repositoryClass="App\Repository\TypeCategorieRepository")
 */
class TypeCategorie
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Categorie", mappedBy="typeCategorie", cascade="remove")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Langue")
     * @ORM\JoinColumn(nullable=false)
     */
    private $langue;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SEOTypeCategorie", inversedBy="typeCategorie", cascade={"remove", "persist"})
     * @Assert\Valid
     */
    private $seo;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function __toString()
    {
        if($this->getNom()){
            return $this->getNom();
        }

        return 'Type de catégorie';
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
     * Set nom
     *
     * @param string $nom
     *
     * @return TypeCategorie
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
     * Set description
     *
     * @param string $description
     *
     * @return TypeCategorie
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add category
     *
     * @param \App\Entity\Categorie $category
     *
     * @return TypeCategorie
     */
    public function addCategory(\App\Entity\Categorie $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \App\Entity\Categorie $category
     */
    public function removeCategory(\App\Entity\Categorie $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
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

    /**
     * Set seo
     *
     * @param \App\Entity\SEOTypeCategorie $seo
     *
     * @return TypeCategorie
     */
    public function setSeo(\App\Entity\SEOTypeCategorie $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return \App\Entity\SEOTypeCategorie
     */
    public function getSeo()
    {
        return $this->seo;
    }
}
