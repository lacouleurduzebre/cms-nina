<?php

namespace App\Service;

use App\Entity\Langue;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Menu du back-office, partagé entre le tableau de bord EasyAdmin et la barre d'administration du front.
 * Chaque entrée : label, icon, url (null pour un intitulé), css_class, enfants.
 */
class MenuAdmin
{
    private $adminUrl;
    private $droits;
    private $doctrine;

    public function __construct(AdminUrl $adminUrl, Droits $droits, ManagerRegistry $doctrine)
    {
        $this->adminUrl = $adminUrl;
        $this->droits = $droits;
        $this->doctrine = $doctrine;
    }

    public function getMenu(): array
    {
        $menu = [
            $this->lien('Tableau de bord', $this->adminUrl->tableauDeBord(), 'fas fa-tachometer-alt'),
            $this->groupe('Contenus', 'fas fa-puzzle-piece', [
                $this->groupe('Pages', '', [
                    $this->lien('Ajouter une page', $this->adminUrl->crud('Page_Active', 'new'), 'fas fa-plus-circle mrs'),
                    $this->lien('Toutes les pages', $this->adminUrl->crud('Page_Active')),
                    $this->lien('Pages à la corbeille', $this->adminUrl->crud('Page_Corbeille')),
                ]),
                $this->lien('Référencement', $this->adminUrl->route('referencement')),
                $this->groupe('Commentaires', '', [
                    $this->lien('Tous les commentaires', $this->adminUrl->crud('Commentaire_Actif')),
                    $this->lien('Commentaires à la corbeille', $this->adminUrl->crud('Commentaire_Corbeille')),
                ]),
                $this->groupe('Catégories', '', [
                    $this->lien('Ajouter une catégorie', $this->adminUrl->crud('Categorie', 'new'), 'fas fa-plus-circle mrs'),
                    $this->lien('Toutes les catégories', $this->adminUrl->crud('Categorie')),
                    $this->lien('Ajouter un type de catégorie', $this->adminUrl->crud('TypeCategorie', 'new'), 'fas fa-plus-circle mrs'),
                    $this->lien('Tous les types de catégories', $this->adminUrl->crud('TypeCategorie')),
                ]),
                $this->groupe('Langues', '', array_filter([
                    $this->lien('Toutes les langues', $this->adminUrl->crud('Langue')),
                    $this->plusieursLangues() ? $this->lien('Traduction des pages', $this->adminUrl->route('traductionsPages'), '', 'traductions') : null,
                    $this->lien('Traduction des templates', $this->adminUrl->route('traductionsTemplates')),
                ])),
                $this->groupe('Interface', '', [
                    $this->lien('Groupes de blocs', $this->adminUrl->crud('GroupeBlocs')),
                    $this->lien('Régions', $this->adminUrl->crud('Region')),
                    $this->lien('Menus', $this->adminUrl->crud('Menu')),
                ]),
            ]),
            $this->lien('Médiathèque', $this->adminUrl->route('mediatheque'), 'fas fa-images'),
            $this->droits->checkDroit('comptes') ? $this->lien('Comptes', $this->adminUrl->crud('Utilisateur'), 'fas fa-users') : null,
            $this->outils(),
        ];

        return array_values(array_filter($menu));
    }

    //Le menu Outils n'est affiché que si l'utilisateur a accès à au moins un outil soumis à un droit
    private function outils(): ?array
    {
        $outils = array_filter([
            $this->droits->checkDroit('config') ? $this->lien('Configuration générale', $this->adminUrl->crud('Configuration', 'edit', 1)) : null,
            $this->droits->checkDroit('themes') ? $this->lien('Thèmes', $this->adminUrl->route('theme')) : null,
            $this->droits->checkDroit('configBlocs') ? $this->lien('Blocs', $this->adminUrl->route('configurationBlocs')) : null,
            $this->droits->checkDroit('sauvegardes') ? $this->lien('Sauvegardes', $this->adminUrl->route('sauvegarde')) : null,
            $this->droits->checkDroit('droits') ? $this->lien('Droits', $this->adminUrl->route('droits')) : null,
        ]);

        if (!$outils) {
            return null;
        }

        $outils[] = $this->lien('Erreurs 404', $this->adminUrl->route('listeErreurs404'));

        return $this->groupe('Outils', 'fas fa-wrench', $outils);
    }

    private function lien(string $label, string $url, string $icon = '', string $cssClass = ''): array
    {
        return ['label' => $label, 'icon' => $icon, 'url' => $url, 'css_class' => $cssClass, 'enfants' => []];
    }

    private function groupe(string $label, string $icon, array $enfants): array
    {
        return ['label' => $label, 'icon' => $icon, 'url' => null, 'css_class' => '', 'enfants' => array_values($enfants)];
    }

    private function plusieursLangues(): bool
    {
        return count($this->doctrine->getRepository(Langue::class)->findBy(['active' => true])) > 1;
    }
}
