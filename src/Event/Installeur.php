<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-07-19
 * Time: 08:54
 */

namespace App\Event;


use App\Entity\Configuration;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Installeur implements EventSubscriberInterface
{
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        $route = $request->get('_route');

        $routesInstalleur = ['installeur', 'installeurTestConnexion', 'installeurEnregistrementPage', 'installeurSuppressionPage'];

        if(isset($route) && !in_array($route, $routesInstalleur)){
            try {
                $this->doctrine->getConnection()->connect();
            } catch (\Exception $e) {
                $redirection = 'installeur/1';
                $event->setController(function () use($redirection) {
                    return new RedirectResponse($redirection);
                });
                return;
            }

            $repoConfig = $this->doctrine->getRepository(Configuration::class);
            try {
                $repoConfig->find(1);
            } catch (\Exception $e) {
                $redirection = 'installeur/2';
                $event->setController(function () use($redirection) {
                    return new RedirectResponse($redirection);
                });
                return;
            }

            $config = $repoConfig->find(1);
            if(!$config || !$config->getInstalle()){
                $redirection = 'installeur/1';
                $event->setController(function () use($redirection) {
                    return new RedirectResponse($redirection);
                });
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(array('onKernelController', 15)),
        );
    }
}