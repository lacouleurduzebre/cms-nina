<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeModule
 *
 * @ORM\Entity(repositoryClass="App\Repository\TypeModuleRepository")
 */
class TypeModule
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
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module", inversedBy="type")
     */
    private $modules;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Champ", inversedBy="modules", cascade={"persist"})
     */
    private $champs;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return TypeModule
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set champs
     *
     * @param array $champs
     *
     * @return TypeModule
     */
    public function setChamps($champs)
    {
        $this->champs = $champs;
    
        return $this;
    }

    /**
     * Get champs
     *
     * @return array
     */
    public function getChamps()
    {
        return $this->champs;
    }

    /**
     * Set modules
     *
     * @param \App\Entity\Module $modules
     *
     * @return TypeModule
     */
    public function setModules(\App\Entity\Module $modules = null)
    {
        $this->modules = $modules;
    
        return $this;
    }

    /**
     * Get modules
     *
     * @return \App\Entity\Module
     */
    public function getModules()
    {
        return $this->modules;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->champs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add champ
     *
     * @param \App\Entity\Champ $champ
     *
     * @return TypeModule
     */
    public function addChamp(\App\Entity\Champ $champ)
    {
        $this->champs[] = $champ;
    
        return $this;
    }

    /**
     * Remove champ
     *
     * @param \App\Entity\Champ $champ
     */
    public function removeChamp(\App\Entity\Champ $champ)
    {
        $this->champs->removeElement($champ);
    }
}
