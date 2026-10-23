<?php

namespace App\Service;

use App\Controller\Admin\CategorieCrudController;
use App\Controller\Admin\CommentaireCorbeilleCrudController;
use App\Controller\Admin\CommentaireCrudController;
use App\Controller\Admin\ConfigurationCrudController;
use App\Controller\Admin\GroupeBlocsCrudController;
use App\Controller\Admin\LangueCrudController;
use App\Controller\Admin\MenuCrudController;
use App\Controller\Admin\PageCorbeilleCrudController;
use App\Controller\Admin\PageCrudController;
use App\Controller\Admin\RegionCrudController;
use App\Controller\Admin\TypeCategorieCrudController;
use App\Controller\Admin\UtilisateurCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Génère les URL du back-office (EasyAdmin 3) à partir des anciens noms d'entités d'EasyAdmin 2
 * (ex : "Page_Active", "Commentaire_Corbeille"), toujours utilisés par les templates et le JS.
 */
class AdminUrl
{
    public const CRUDS = [
        'Page_Active' => PageCrudController::class,
        'Page_Corbeille' => PageCorbeilleCrudController::class,
        'Commentaire_Actif' => CommentaireCrudController::class,
        'Commentaire_Corbeille' => CommentaireCorbeilleCrudController::class,
        'Categorie' => CategorieCrudController::class,
        'TypeCategorie' => TypeCategorieCrudController::class,
        'Langue' => LangueCrudController::class,
        'GroupeBlocs' => GroupeBlocsCrudController::class,
        'Region' => RegionCrudController::class,
        'Menu' => MenuCrudController::class,
        'Utilisateur' => UtilisateurCrudController::class,
        'Configuration' => ConfigurationCrudController::class,
    ];

    //Actions EasyAdmin 2 => EasyAdmin 3 (les actions personnalisées gardent leur nom)
    private const ACTIONS = [
        'list' => Action::INDEX,
        'search' => Action::INDEX,
        'show' => Action::DETAIL,
    ];

    //Paramètres d'URL d'EasyAdmin 2 non repris par EasyAdmin 3
    private const PARAMETRES_EASYADMIN2 = ['entity', 'action', 'id', 'menuIndex', 'submenuIndex', 'referer', 'sortField', 'sortDirection', 'page'];

    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function existe(?string $entite): bool
    {
        return null !== $entite && isset(self::CRUDS[$entite]);
    }

    public function crud(string $entite, string $action = Action::INDEX, $id = null, array $parametres = []): string
    {
        if (!self::existe($entite)) {
            throw new \InvalidArgumentException(sprintf('Entité d\'administration inconnue : "%s"', $entite));
        }

        $url = $this->adminUrlGenerator
            ->unsetAll()
            ->setController(self::CRUDS[$entite])
            ->setAction(self::ACTIONS[$action] ?? $action);

        if (null !== $id && '' !== $id) {
            $url->setEntityId($id);
        }

        foreach ($parametres as $nom => $valeur) {
            $url->set($nom, $valeur);
        }

        return $url->generateUrl();
    }

    //Page du back-office hors CRUD (tableau de bord, droits, thèmes...) affichée dans le layout d'EasyAdmin
    public function route(string $route, array $parametresRoute = [], array $query = []): string
    {
        $url = $this->adminUrlGenerator->unsetAll()->setRoute($route, $parametresRoute);
        foreach ($query as $nom => $valeur) {
            $url->set($nom, $valeur);
        }

        return $url->generateUrl();
    }

    public function tableauDeBord(): string
    {
        return $this->adminUrlGenerator->unsetAll()->generateUrl();
    }

    //Ancienne URL d'EasyAdmin 2 (/admin/?entity=Page_Active&action=edit&id=1) : URL équivalente d'EasyAdmin 3, ou null
    public function depuisAncienneUrl(Request $request): ?string
    {
        $entite = $request->query->get('entity');
        if (!self::existe($entite)) {
            return null;
        }

        //Paramètres propres au CMS (ex : ?categorie=X, ?pageOriginale=X&langue=Y) et recherche (?query=)
        $parametres = array_diff_key($request->query->all(), array_flip(self::PARAMETRES_EASYADMIN2));

        return $this->crud($entite, $request->query->get('action', 'list'), $request->query->get('id'), $parametres);
    }
}
