<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 *
 * @ORM\Table(name="module")
 * @ORM\Entity(repositoryClass="App\Repository\ModuleRepository")
 */
class Module
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Page", mappedBy="modules")
     */
    private $pages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TypeModule", mappedBy="modules")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\Column(name="contenu", type="array")
     */
    private $contenu = array();

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
     * @return Module
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
     * Constructor
     */
    public function __construct()
    {
        $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add page
     *
     * @param \App\Entity\Page $page
     *
     * @return Module
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
     * Add type
     *
     * @param \App\Entity\TypeModule $type
     *
     * @return Module
     */
    public function addType(\App\Entity\TypeModule $type)
    {
        $this->type[] = $type;
    
        return $this;
    }

    /**
     * Remove type
     *
     * @param \App\Entity\TypeModule $type
     */
    public function removeType(\App\Entity\TypeModule $type)
    {
        $this->type->removeElement($type);
    }

    /**
     * Get type
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set contenu
     *
     * @param array $contenu
     *
     * @return Module
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    
        return $this;
    }

    /**
     * Get contenu
     *
     * @return array
     */
    public function getContenu()
    {
        return $this->contenu;
    }
}
