<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GroupeBlocs", mappedBy="region", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $groupesBlocs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identifiant;

    public function __construct()
    {
        $this->groupesBlocs = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|GroupeBlocs[]
     */
    public function getGroupesBlocs(): Collection
    {
        return $this->groupesBlocs;
    }

    public function addGroupesBloc(GroupeBlocs $groupesBloc): self
    {
        if (!$this->groupesBlocs->contains($groupesBloc)) {
            $this->groupesBlocs[] = $groupesBloc;
            $groupesBloc->setRegion($this);
        }

        return $this;
    }

    public function removeGroupesBloc(GroupeBlocs $groupesBloc): self
    {
        if ($this->groupesBlocs->contains($groupesBloc)) {
            $this->groupesBlocs->removeElement($groupesBloc);
            // set the owning side to null (unless already changed)
            if ($groupesBloc->getRegion() === $this) {
                $groupesBloc->setRegion(null);
            }
        }

        return $this;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }
}
