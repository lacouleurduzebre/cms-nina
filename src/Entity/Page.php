<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * page
 *
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 */
class Page
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
     * @ORM\Column(name="titre", type="text")
     */
    private $titre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="pages")
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="pagesModifiees")
     */
    private $auteurDerniereModification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_publication", type="datetime")
     */
    private $datePublication;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_depublication", type="datetime", nullable=true)
     */
    private $dateDepublication;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Page", mappedBy="pageOriginale")
     */
    private $pagesTraduites = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="pagesTraduites")
     * @ORM\JoinColumn(nullable=true)
     */
    private $pageOriginale = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Langue", inversedBy="pages")
     */
    private $langue;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaire", mappedBy="page", cascade={"remove", "persist"})
     */
    private $commentaires;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="corbeille", type="boolean")
     */
    private $corbeille = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SEO", inversedBy="page", cascade={"remove", "persist"})
     */
    private $seo;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Categorie", inversedBy="pages", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module", mappedBy="page", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $modules;

    public function __construct()
    {
        $this->datePublication = new \DateTime();
        $this->dateCreation = new \DateTime();
        $this->commentaires = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->pagesTraduites = new ArrayCollection();
        $this->modules = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitre();
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
     * Set titre
     *
     * @param string $titre
     *
     * @return Page
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Page
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Page
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set corbeille
     *
     * @param string $corbeille
     *
     * @return Page
     */
    public function setCorbeille($corbeille)
    {
        $this->corbeille = $corbeille;

        return $this;
    }

    /**
     * Get corbeille
     *
     * @return string
     */
    public function getCorbeille()
    {
        return $this->corbeille;
    }

    /**
     * Add commentaire
     *
     * @param \App\Entity\Commentaire $commentaire
     *
     * @return Page
     */
    public function addCommentaire(\App\Entity\Commentaire $commentaire)
    {
        $commentaire->setPage($this);
        $this->commentaires->add($commentaire);

        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param \App\Entity\Commentaire $commentaire
     */
    public function removeCommentaire(\App\Entity\Commentaire $commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }

    /**
     * Get commentaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * Set seo
     *
     * @param \App\Entity\SEO $seo
     *
     * @return Page
     */
    public function setSeo(\App\Entity\SEO $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return \App\Entity\SEO
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Add category
     *
     * @param \App\Entity\Categorie $category
     *
     * @return Page
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

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Page
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set pageOriginale
     *
     * @param \App\Entity\Page $pageOriginale
     *
     * @return Page
     */
    public function setPageOriginale(\App\Entity\Page $pageOriginale = null)
    {
        $this->pageOriginale = $pageOriginale;
    
        return $this;
    }

    /**
     * Get pageOriginale
     *
     * @return \App\Entity\Page
     */
    public function getPageOriginale()
    {
        return $this->pageOriginale;
    }

    /**
     * Set langue
     *
     * @param \App\Entity\Langue $langue
     *
     * @return Page
     */
    public function setLangue(\App\Entity\Langue $langue = null)
    {
        $this->langue = $langue;
    
        return $this;
    }

    /**
     * Get langue
     *
     * @return \App\Entity\Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Add pagesTraduite
     *
     * @param \App\Entity\Page $pagesTraduite
     *
     * @return Page
     */
    public function addPagesTraduite(\App\Entity\Page $pagesTraduite)
    {
        $this->pagesTraduites[] = $pagesTraduite;
    
        return $this;
    }

    /**
     * Remove pagesTraduite
     *
     * @param \App\Entity\Page $pagesTraduite
     */
    public function removePagesTraduite(\App\Entity\Page $pagesTraduite)
    {
        $this->pagesTraduites->removeElement($pagesTraduite);
    }

    /**
     * Get pagesTraduites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagesTraduites()
    {
        return $this->pagesTraduites;
    }

    /**
     * Set auteur
     *
     * @param \App\Entity\Utilisateur $auteur
     *
     * @return Page
     */
    public function setAuteur(\App\Entity\Utilisateur $auteur = null)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return \App\Entity\Utilisateur
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set auteurDerniereModification
     *
     * @param \App\Entity\Utilisateur $auteurDerniereModification
     *
     * @return Page
     */
    public function setAuteurDerniereModification(\App\Entity\Utilisateur $auteurDerniereModification = null)
    {
        $this->auteurDerniereModification = $auteurDerniereModification;
    
        return $this;
    }

    /**
     * Get auteurDerniereModification
     *
     * @return \App\Entity\Utilisateur
     */
    public function getAuteurDerniereModification()
    {
        return $this->auteurDerniereModification;
    }

    /**
     * Set datePublication
     *
     * @param \DateTime $datePublication
     *
     * @return Page
     */
    public function setDatePublication($datePublication)
    {
        $this->datePublication = $datePublication;
    
        return $this;
    }

    /**
     * Get datePublication
     *
     * @return \DateTime
     */
    public function getDatePublication()
    {
        return $this->datePublication;
    }

    /**
     * Set dateDepublication
     *
     * @param \DateTime $dateDepublication
     *
     * @return Page
     */
    public function setDateDepublication($dateDepublication)
    {
        $this->dateDepublication = $dateDepublication;
    
        return $this;
    }

    /**
     * Get dateDepublication
     *
     * @return \DateTime
     */
    public function getDateDepublication()
    {
        return $this->dateDepublication;
    }

    /**
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setPage($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getPage() === $this) {
                $module->setPage(null);
            }
        }

        return $this;
    }
}
