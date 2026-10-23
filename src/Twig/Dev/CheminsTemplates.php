<?php

namespace App\Twig\Dev;

use Twig\Extension\AbstractExtension;

/**
 * Dev uniquement (cf. config/services_dev.yaml) : chemins des templates en commentaires HTML
 */
class CheminsTemplates extends AbstractExtension
{
    private $racine;

    public function __construct(string $racine)
    {
        $this->racine = $racine;
    }

    public function getNodeVisitors(): array
    {
        return [new CheminsTemplatesVisitor($this->racine)];
    }
}
