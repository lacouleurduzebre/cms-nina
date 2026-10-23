<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Extraction des segments à traduire des templates et du code vers des fichiers XLIFF 2.0
 * (remplace la commande translation:extract de php-translation/symfony-bundle)
 *
 * Les traductions existantes sont conservées, les nouveaux segments sont ajoutés sans traduction
 */
class ExtractionTraductions
{
    private const CONFIGURATIONS = [
        'back' => [
            'dossiers' => ['templates/back', 'src'],
            'exclusions' => ['cache', 'data', 'logs', 'Blocs'],
            'sortie' => 'translations/back',
        ],
        'front' => [
            'dossiers' => ['templates/bundles', 'templates/front', 'templates/securite', 'src/Blocs'],
            'exclusions' => ['cache', 'data', 'logs'],
            'sortie' => 'translations/front',
        ],
        'theme' => [
            'dossiers' => ['themes/{theme}'],
            'exclusions' => [],
            'sortie' => 'themes/{theme}/translations',
        ],
    ];

    private const NS = 'urn:oasis:names:tc:xliff:document:2.0';

    public function __construct(
        private ExtractorInterface $extracteur,
        #[Autowire('%kernel.project_dir%')] private string $racine,
        #[Autowire('%kernel.default_locale%')] private string $langueSource,
    ) {
    }

    public function extraire(string $configuration, string $locale, string $theme): void
    {
        $config = self::CONFIGURATIONS[$configuration];

        $dossiers = array_filter(array_map(fn ($dossier) => $this->racine.'/'.str_replace('{theme}', $theme, $dossier), $config['dossiers']), 'is_dir');
        if (!$dossiers) {
            return;
        }

        $fichiers = (new Finder())->files()->in($dossiers)->exclude($config['exclusions'])->notName(['*TestCase.php', '*Test.php'])->sortByName();

        $catalogue = new MessageCatalogue($locale);
        $this->extracteur->extract(array_map(fn ($fichier) => $fichier->getPathname(), iterator_to_array($fichiers, false)), $catalogue);

        $sortie = $this->racine.'/'.str_replace('{theme}', $theme, $config['sortie']);
        if (!is_dir($sortie)) {
            mkdir($sortie, 0777, true);
        }

        foreach ($catalogue->getDomains() as $domaine) {
            $fichier = $sortie.'/'.$domaine.'.'.$locale.'.xlf';
            $segments = $this->lire($fichier);

            foreach (array_keys($catalogue->all($domaine)) as $source) {
                if (!array_key_exists($source, $segments)) {
                    $segments[$source] = ['id' => $this->identifiant($source), 'target' => null];
                }
            }

            $this->ecrire($fichier, $domaine, $locale, $segments);
        }
    }

    /**
     * Segments d'un fichier existant : [source => ['id' => ..., 'target' => ...]]
     */
    private function lire(string $fichier): array
    {
        $segments = [];

        if (!is_file($fichier) || !$document = simplexml_load_file($fichier, 'SimpleXMLElement', LIBXML_NOWARNING)) {
            return $segments;
        }

        $document->registerXPathNamespace('u', self::NS);
        foreach ($document->xpath('//u:unit') as $unit) {
            $source = isset($unit['name']) ? (string) $unit['name'] : (string) $unit->segment->source;
            $segments[$source] = [
                'id' => (string) $unit['id'],
                'target' => isset($unit->segment->target) ? (string) $unit->segment->target : null,
            ];
        }

        return $segments;
    }

    private function ecrire(string $fichier, string $domaine, string $locale, array $segments): void
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $xliff = $dom->appendChild($dom->createElementNS(self::NS, 'xliff'));
        $xliff->setAttribute('version', '2.0');
        $xliff->setAttribute('srcLang', str_replace('_', '-', $this->langueSource));
        $xliff->setAttribute('trgLang', str_replace('_', '-', $locale));

        $file = $xliff->appendChild($dom->createElement('file'));
        $file->setAttribute('id', $domaine.'.'.$locale);

        foreach ($segments as $source => $segment) {
            $unit = $file->appendChild($dom->createElement('unit'));
            $unit->setAttribute('id', $segment['id']);
            $unit->setAttribute('name', $source);

            $noeud = $unit->appendChild($dom->createElement('segment'));
            $noeud->appendChild($dom->createElement('source'))->appendChild($dom->createTextNode($source));
            //Sans traduction, Symfony affiche la source
            if (null !== $segment['target']) {
                $noeud->appendChild($dom->createElement('target'))->appendChild($dom->createTextNode($segment['target']));
            }
        }

        $dom->save($fichier);
    }

    //Même identifiant que le XliffFileDumper de Symfony
    private function identifiant(string $source): string
    {
        return strtr(substr(base64_encode(hash('xxh128', $source, true)), 0, 7), '/+', '._');
    }
}
