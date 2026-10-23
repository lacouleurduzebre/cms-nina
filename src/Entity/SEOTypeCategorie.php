<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * SEOTypeCategorie
 */
#[ORM\Entity(repositoryClass: \App\Repository\SEOTypeCategorieRepository::class)]
#[UniqueEntity(fields: ['url'], message: "L'url {{ value }} est déjà utilisée pour un autre type de catégorie")]
class SEOTypeCategorie extends SEO
{
    #[ORM\OneToOne(targetEntity: \App\Entity\TypeCategorie::class, mappedBy: 'seo')]
    private $typeCategorie;

    /**
     * Set typeCategorie
     *
     * @param \App\Entity\TypeCategorie $typeCategorie
     *
     * @return SEO
     */
    public function setTypeCategorie(?\App\Entity\TypeCategorie $typeCategorie = null)
    {
        $this->typeCategorie = $typeCategorie;

        return $this;
    }

    /**
     * Get typeCategorie
     *
     * @return \App\Entity\TypeCategorie
     */
    public function getTypeCategorie()
    {
        return $this->typeCategorie;
    }
}
