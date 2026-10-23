<?php

namespace App\Twig\Dev;

use Twig\Environment;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Node\Nodes;
use Twig\Node\TextNode;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * Dev : encadre le rendu de chaque template HTML par des commentaires indiquant son chemin
 * (<!-- /templates/... --> ... <!-- FIN /templates/... -->), sauf pour les templates "Brut".
 */
class CheminsTemplatesVisitor implements NodeVisitorInterface
{
    private $racine;

    public function __construct(string $racine)
    {
        $this->racine = $racine;
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        if (!$node instanceof ModuleNode) {
            return $node;
        }

        $chemin = str_replace($this->racine, '', $node->getSourceContext()->getPath());
        if ('html.twig' !== substr($chemin, -9) || false !== strpos($chemin, 'Brut')) {
            return $node;
        }

        $node->setNode('display_start', new Nodes([new TextNode('<!-- '.$chemin.' -->', 0), $node->getNode('display_start')]));
        $node->setNode('display_end', new Nodes([$node->getNode('display_end'), new TextNode('<!-- FIN '.$chemin.' -->', 0)]));

        return $node;
    }

    public function getPriority(): int
    {
        return 0;
    }
}
