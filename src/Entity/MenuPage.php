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
     * @ORM\JoinColumn(nullable=true)
     */
    private $pageParent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Menu", inversedBy="menuPage")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $menu;

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
     * Set pageParent
     *
     * @param integer $pageParent
     *
     * @return MenuPage
     */
    public function setPageParent($pageParent)
    {
        $this->pageParent = $pageParent;
    
        return $this;
    }

    /**
     * Get pageParent
     *
     * @return integer
     */
    public function getPageParent()
    {
        return $this->pageParent;
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
    public function setMenu(\App\Entity\Menu $menu)
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
}
