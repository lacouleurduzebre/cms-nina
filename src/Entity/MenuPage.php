<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuPage
 *
 * @ORM\Entity(repositoryClass="App\Repository\MenuPageRepository")
 */
class MenuPage
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
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Menu", inversedBy="menuPage")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $menu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MenuPage")
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titreUrl;

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
     * Set position
     *
     * @param integer $position
     *
     * @return MenuPage
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set page
     *
     * @param \App\Entity\Page $page
     *
     * @return MenuPage
     */
    public function setPage(\App\Entity\Page $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \App\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set menu
     *
     * @param \App\Entity\Menu $menu
     *
     * @return MenuPage
     */
    public function setMenu(\App\Entity\Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \App\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTitreUrl(): ?string
    {
        return $this->titreUrl;
    }

    public function setTitreUrl(?string $titreUrl): self
    {
        $this->titreUrl = $titreUrl;

        return $this;
    }
}
