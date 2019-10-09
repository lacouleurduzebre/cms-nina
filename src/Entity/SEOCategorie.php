<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * SEOCategorie
 *
 * @ORM\Entity(repositoryClass="App\Repository\SEOCategorieRepository")
 * @UniqueEntity(fields={"url"}, message="L'url {{ value }} est déjà utilisée pour une autre catégorie")
 */
class SEOCategorie extends SEO
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Categorie", mappedBy="seo")
     */
    private $categorie;

    /**
     * Set categorie
     *
     * @param \App\Entity\Categorie $categorie
     *
     * @return SEO
     */
    public function setCategorie(\App\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \App\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}
