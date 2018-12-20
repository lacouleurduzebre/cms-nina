<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Bloc", mappedBy="page", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titreMenu;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $traductions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlocAnnexe", mappedBy="page", orphanRemoval=true, cascade={"persist"})
     */
    private $blocsAnnexes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $affichageSousNiveaux = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $affichageCommentaires = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $affichageDatePublication = true;

    public function __construct()
    {
        $this->datePublication = new \DateTime();
        $this->dateCreation = new \DateTime();
        $this->commentaires = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->blocs = new ArrayCollection();
        $this->blocsAnnexes = new ArrayCollection();
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
     * @return Collection|Bloc[]
     */
    public function getBlocs(): Collection
    {
        return $this->blocs;
    }

    public function addBloc(Bloc $bloc): self
    {
        if (!$this->blocs->contains($bloc)) {
            $this->blocs[] = $bloc;
            $bloc->setPage($this);
        }

        return $this;
    }

    public function removeBloc(Bloc $bloc): self
    {
        if ($this->blocs->contains($bloc)) {
            $this->blocs->removeElement($bloc);
            // set the owning side to null (unless already changed)
            if ($bloc->getPage() === $this) {
                $bloc->setPage(null);
            }
        }

        return $this;
    }

    public function getTitreMenu(): ?string
    {
        return $this->titreMenu;
    }

    public function setTitreMenu(?string $titreMenu): self
    {
        $this->titreMenu = $titreMenu;

        return $this;
    }

    public function getTraductions(): ?array
    {
        return $this->traductions;
    }

    public function setTraductions(?array $traductions): self
    {
        $this->traductions = $traductions;

        return $this;
    }

    /**
     * @return Collection|BlocAnnexe[]
     */
    public function getBlocsAnnexes(): Collection
    {
        return $this->blocsAnnexes;
    }

    public function addBlocsAnnex(BlocAnnexe $blocsAnnex): self
    {
        if (!$this->blocsAnnexes->contains($blocsAnnex)) {
            $this->blocsAnnexes[] = $blocsAnnex;
            $blocsAnnex->setPage($this);
        }

        return $this;
    }

    public function removeBlocsAnnex(BlocAnnexe $blocsAnnex): self
    {
        if ($this->blocsAnnexes->contains($blocsAnnex)) {
            $this->blocsAnnexes->removeElement($blocsAnnex);
            // set the owning side to null (unless already changed)
            if ($blocsAnnex->getPage() === $this) {
                $blocsAnnex->setPage(null);
            }
        }

        return $this;
    }

    public function getAffichageSousNiveaux(): ?bool
    {
        return $this->affichageSousNiveaux;
    }

    public function setAffichageSousNiveaux(?bool $affichageSousNiveaux): self
    {
        $this->affichageSousNiveaux = $affichageSousNiveaux;

        return $this;
    }

    public function getAffichageCommentaires(): ?bool
    {
        return $this->affichageCommentaires;
    }

    public function setAffichageCommentaires(bool $affichageCommentaires): self
    {
        $this->affichageCommentaires = $affichageCommentaires;

        return $this;
    }

    public function getAffichageDatePublication(): ?bool
    {
        return $this->affichageDatePublication;
    }

    public function setAffichageDatePublication(bool $affichageDatePublication): self
    {
        $this->affichageDatePublication = $affichageDatePublication;

        return $this;
    }
}
