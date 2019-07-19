<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-07-19
 * Time: 08:54
 */

namespace App\Event;


use App\Controller\Back\InstalleurController;
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

        if($route != 'installeur'){
            /*try {
                $this->getDoctrine()->getConnection()->connect();
            } catch (\Exception $e) {
                return $this->redirectToRoute('installeur', ['etape' => 1]);
            }
            $repoConfig = $this->getDoctrine()->getRepository(Configuration::class);
            try {
                $repoConfig->find(1);
            } catch (\Exception $e) {
                return $this->redirectToRoute('installeur', ['etape' => 2]);
            }*/

            $repoConfig = $this->doctrine->getRepository(Configuration::class);
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