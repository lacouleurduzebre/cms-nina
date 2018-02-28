<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Utilisateur
 *
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
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
}
