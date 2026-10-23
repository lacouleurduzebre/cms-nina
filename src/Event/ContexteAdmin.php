<?php

namespace App\Event;

use App\Service\AdminUrl;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Les pages du back-office hors CRUD (droits, thèmes, sauvegardes...) utilisent le layout d'EasyAdmin,
 * qui a besoin du contexte d'EasyAdmin : elles doivent être appelées via le tableau de bord
 * (/admin?routeName=droits). Les accès directs (/admin/droits) y sont redirigés.
 */
class ContexteAdmin implements EventSubscriberInterface
{
    //Routes d'EasyAdmin et routes de compatibilité
    private const ROUTES_EXCLUES = ['admin', 'easyadmin', 'tableauDeBord'];

    private $adminUrl;

    public function __construct(AdminUrl $adminUrl)
    {
        $this->adminUrl = $adminUrl;
    }

    public static function getSubscribedEvents(): array
    {
        //Après le routeur (32) et le routeur d'EasyAdmin (0)
        return [KernelEvents::REQUEST => ['redirection', -10]];
    }

    public function redirection(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (!$event->isMainRequest()
            || !$request->isMethod('GET')
            || $request->isXmlHttpRequest()
            || null === $route
            || in_array($route, self::ROUTES_EXCLUES, true)
            || 0 !== strpos($request->getPathInfo(), '/admin/')
            || $request->attributes->has(EA::CONTEXT_REQUEST_ATTRIBUTE)
        ) {
            return;
        }

        $url = $this->adminUrl->route($route, $request->attributes->get('_route_params', []), $request->query->all());

        $event->setResponse(new RedirectResponse($url));
    }
}
