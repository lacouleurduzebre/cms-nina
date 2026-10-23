<?php

namespace App\Controller\Admin;

use App\Service\AdminUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Route "easyadmin" d'EasyAdmin 2, encore utilisée par le JS (Routing.generate('easyadmin', {entity, action, id})).
 * Elle a le même chemin que le tableau de bord, qui est prioritaire et redirige les anciennes URL
 * (cf. DashboardController::index et AdminUrl::depuisAncienneUrl) : elle ne sert qu'à générer des URL.
 */
class AncienneUrlController extends AbstractController
{
    #[Route(path: '/admin', name: 'easyadmin', priority: -10)]
    public function redirection(Request $request, AdminUrl $adminUrl): RedirectResponse
    {
        return $this->redirect($adminUrl->depuisAncienneUrl($request) ?? $adminUrl->tableauDeBord());
    }
}
