<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlocRepository")
 */
class Bloc
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $contenu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="blocs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $page;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="array", length=255, nullable=true)
     */
    private $classes;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bloc", inversedBy="blocsEnfants", cascade={"persist"})
     */
    private $blocParent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bloc", mappedBy="blocParent", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocsEnfants;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $largeur = 'col12';

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $padding;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $alignementVertical;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $alignementHorizontal;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $alignementHorizontalEnfants;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $alignementVerticalEnfants;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pleineLargeur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region", inversedBy="blocs")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $gouttieres;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\BlocPartage", mappedBy="bloc", orphanRemoval=true)
     */
    private $blocPartage;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $alignementContenu;

    public function __construct()
    {
        $this->blocsEnfants = new ArrayCollection();
    }

    public function __clone(){
        if ($this->id) {
            $this->setBlocPartage(null);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getContenu(): ?array
    {
        return $this->contenu;
    }

    public function setContenu(?array $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getClasses(): ?array
    {
        return $this->classes;
    }

    public function setClasses(?array $classes): self
    {
        $this->classes = $classes;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getBlocParent(): ?self
    {
        return $this->blocParent;
    }

    public function setBlocParent(?self $blocParent): self
    {
        $this->blocParent = $blocParent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getBlocsEnfants(): Collection
    {
        return $this->blocsEnfants;
    }

    public function addBlocsEnfant(self $blocsEnfant): self
    {
        if (!$this->blocsEnfants->contains($blocsEnfant)) {
            $this->blocsEnfants[] = $blocsEnfant;
            $blocsEnfant->setBlocParent($this);
        }

        return $this;
    }

    public function removeBlocsEnfant(self $blocsEnfant): self
    {
        if ($this->blocsEnfants->contains($blocsEnfant)) {
            $this->blocsEnfants->removeElement($blocsEnfant);
            // set the owning side to null (unless already changed)
            if ($blocsEnfant->getBlocParent() === $this) {
                $blocsEnfant->setBlocParent(null);
            }
        }

        return $this;
    }

    public function getLargeur(): ?string
    {
        return $this->largeur;
    }

    public function setLargeur(?string $largeur): self
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getPadding(): ?string
    {
        return $this->padding;
    }

    public function setPadding(?string $padding): self
    {
        $this->padding = $padding;

        return $this;
    }

    public function getAlignementVertical(): ?string
    {
        return $this->alignementVertical;
    }

    public function setAlignementVertical(?string $alignementVertical): self
    {
        $this->alignementVertical = $alignementVertical;

        return $this;
    }

    public function getAlignementHorizontal(): ?string
    {
        return $this->alignementHorizontal;
    }

    public function setAlignementHorizontal(?string $alignementHorizontal): self
    {
        $this->alignementHorizontal = $alignementHorizontal;

        return $this;
    }

    public function getAlignementHorizontalEnfants(): ?string
    {
        return $this->alignementHorizontalEnfants;
    }

    public function setAlignementHorizontalEnfants(?string $alignementHorizontalEnfants): self
    {
        $this->alignementHorizontalEnfants = $alignementHorizontalEnfants;

        return $this;
    }

    public function getAlignementVerticalEnfants(): ?string
    {
        return $this->alignementVerticalEnfants;
    }

    public function setAlignementVerticalEnfants(?string $alignementVerticalEnfants): self
    {
        $this->alignementVerticalEnfants = $alignementVerticalEnfants;

        return $this;
    }

    public function getPleineLargeur(): ?bool
    {
        return $this->pleineLargeur;
    }

    public function setPleineLargeur(?bool $pleineLargeur): self
    {
        $this->pleineLargeur = $pleineLargeur;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getGouttieres(): ?string
    {
        return $this->gouttieres;
    }

    public function setGouttieres(?string $gouttieres): self
    {
        $this->gouttieres = $gouttieres;

        return $this;
    }

    public function getBlocPartage(): ?BlocPartage
    {
        return $this->blocPartage;
    }

    public function setBlocPartage(BlocPartage $blocPartage = null): self
    {
        $this->blocPartage = $blocPartage;

        // set the owning side of the relation if necessary
        if ($blocPartage && $this !== $blocPartage->getBloc()) {
            $blocPartage->setBloc($this);
        }

        return $this;
    }

    public function getAlignementContenu(): ?string
    {
        return $this->alignementContenu;
    }

    public function setAlignementContenu(?string $alignementContenu): self
    {
        $this->alignementContenu = $alignementContenu;

        return $this;
    }
}
