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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $htmlAvant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $htmlApres;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GroupeBlocs", inversedBy="blocs")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $groupeBlocs;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bloc", inversedBy="blocsEnfants", cascade={"persist"})
     */
    private $blocParent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bloc", mappedBy="blocParent", cascade={"persist"})
     */
    private $blocsEnfants;

    public function __construct()
    {
        $this->blocsEnfants = new ArrayCollection();
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

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getHtmlAvant(): ?string
    {
        return $this->htmlAvant;
    }

    public function setHtmlAvant(?string $htmlAvant): self
    {
        $this->htmlAvant = $htmlAvant;

        return $this;
    }

    public function getHtmlApres(): ?string
    {
        return $this->htmlApres;
    }

    public function setHtmlApres(?string $htmlApres): self
    {
        $this->htmlApres = $htmlApres;

        return $this;
    }

    public function getGroupeBlocs(): ?GroupeBlocs
    {
        return $this->groupeBlocs;
    }

    public function setGroupeBlocs(?GroupeBlocs $groupeBlocs): self
    {
        $this->groupeBlocs = $groupeBlocs;

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
}
