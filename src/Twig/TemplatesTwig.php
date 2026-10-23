<?php

namespace App\Twig;

/**
 * Templates précompilés par le préchauffage du cache de Twig :
 * - uniquement les fichiers .twig (les chemins Twig incluent src/, pour les templates des blocs) ;
 * - en mode debug, sans les templates des bundles (@EasyAdmin, @WebProfiler...), compilés à la demande,
 *   pour que le préchauffage du cache de dev reste sous 128 Mo.
 * Le filtre sur l'extension pourra être remplacé par l'option twig.file_name_pattern à partir de Symfony 6.1.
 */
class TemplatesTwig implements \IteratorAggregate
{
    private $templates;
    private $debug;

    public function __construct(\IteratorAggregate $templates, bool $debug)
    {
        $this->templates = $templates;
        $this->debug = $debug;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->templates as $template) {
            if ('.twig' !== substr($template, -5) || ($this->debug && '@' === $template[0])) {
                continue;
            }

            yield $template;
        }
    }
}
