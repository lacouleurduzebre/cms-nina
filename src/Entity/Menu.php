<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255)
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MenuPage", mappedBy="menu", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $menuPage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuPage = new ArrayCollection();
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
     * Set region
     *
     * @param string $region
     *
     * @return Menu
     */
    public function setRegion($region)
    {
        $this->region = $region;
    
        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
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
}
