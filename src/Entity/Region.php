<?php

namespace App\Entity;

use App\Controller\SEOController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $identifiant;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bloc", mappedBy="region", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocs;

    public function __construct()
    {
        $this->groupesBlocs = new ArrayCollection();
        $this->blocs = new ArrayCollection();
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

    public function setNom($nom)
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

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;

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
            $bloc->setRegion($this);
        }

        return $this;
    }

    public function removeBloc(Bloc $bloc): self
    {
        if ($this->blocs->contains($bloc)) {
            $this->blocs->removeElement($bloc);
            // set the owning side to null (unless already changed)
            if ($bloc->getRegion() === $this) {
                $bloc->setRegion(null);
            }
        }

        return $this;
    }
}
