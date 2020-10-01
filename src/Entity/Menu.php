<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Menu
 *
 * @ORM\Entity(repositoryClass="App\Repository\MenuRepository")
 */
class Menu
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
     * @ORM\OneToMany(targetEntity="App\Entity\MenuPage", mappedBy="menu", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $menuPage;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $defaut;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Langue")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $langue;

    /**
     * @ORM\Column(type="smallint", options={"default" : 1})
     */
    private $priorite;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuPage = new ArrayCollection();
        $this->defaut = false;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * Get id
     *
     * @return integer
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
     * @return Menu
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
     * Add menuPage
     *
     * @param \App\Entity\MenuPage $menuPage
     *
     * @return Menu
     */
    public function addMenuPage(\App\Entity\MenuPage $menuPage)
    {
        $this->menuPage[] = $menuPage;

        $menuPage->setMenu($this);

        return $this;
    }

    /**
     * Remove menuPage
     *
     * @param \App\Entity\MenuPage $menuPage
     */
    public function removeMenuPage(\App\Entity\MenuPage $menuPage)
    {
        $this->menuPage->removeElement($menuPage);
    }

    /**
     * Get menuPage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMenuPage()
    {
        return $this->menuPage;
    }

    public function getDefaut(): ?bool
    {
        return $this->defaut;
    }

    public function setDefaut(?bool $defaut): self
    {
        $this->defaut = $defaut;

        return $this;
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

    public function getPriorite(): ?int
    {
        return $this->priorite;
    }

    public function setPriorite(int $priorite): self
    {
        $this->priorite = $priorite;

        return $this;
    }
}
