<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupeBlocsRepository")
 */
class GroupeBlocs
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Langue")
     * @ORM\JoinColumn(nullable=false)
     */
    private $langue;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bloc", mappedBy="groupeBlocs", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocs;

    public function __construct()
    {
        $this->blocs = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom();
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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getLangue(): ?Langue
    {
        return $this->langue;
    }

    public function setLangue(?Langue $langue): self
    {
        $this->langue = $langue;

        return $this;
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
            $bloc->setGroupeBlocs($this);
        }

        return $this;
    }

    public function removeBloc(Bloc $bloc): self
    {
        if ($this->blocs->contains($bloc)) {
            $this->blocs->removeElement($bloc);
            // set the owning side to null (unless already changed)
            if ($bloc->getGroupeBlocs() === $this) {
                $bloc->setGroupeBlocs(null);
            }
        }

        return $this;
    }
}
