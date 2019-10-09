<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * SEOPage
 *
 * @ORM\Entity(repositoryClass="App\Repository\SEOPageRepository")
 * @UniqueEntity(fields={"url"}, message="L'url {{ value }} est déjà utilisée pour une autre page")
 */
class SEOPage extends SEO
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Page", mappedBy="seo")
     */
    private $page;

    /**
     * Set page
     *
     * @param \App\Entity\Page $page
     *
     * @return SEO
     */
    public function setPage(\App\Entity\Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \App\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }
}
