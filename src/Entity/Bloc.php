<?php

namespace App\Entity;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="blocs")
     * @ORM\JoinColumn(nullable=false)
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
}
