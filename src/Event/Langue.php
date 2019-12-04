<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2017
 * Time: 10:28
 */

namespace App\Event;


use App\Entity\Configuration;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class Langue implements EventSubscriberInterface
{
    public function __construct(ObjectManager $manager, Security $security, RegistryInterface $doctrine)
    {
        $this->manager = $manager;
        $this->doctrine = $doctrine;
        $this->security = $security;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $repoConfig = $this->doctrine->getRepository(Configuration::class);
        try {
            $repoConfig->find(1);
        } catch (\Exception $e) {
            return;
        }

        $config = $repoConfig->find(1);
        if(!$config || !$config->getInstalle()){
            return;
        }

        $request = $event->getRequest();

        if(substr($request->getRequestUri(), 0, 7) == '/admin/'){//Back-office
            $user = $this->security->getUser();

            if($user->getLangue()){
                $langue = $user->getLangue()->getAbreviation();
            }
        }elseif($locale = $request->attributes->get('_locale')) {//Front-office
            $langue = $locale;
        }

        if(!isset($langue)){
            $repoLangue = $this->manager->getRepository(\App\Entity\Langue::class);
            $langue = $repoLangue->findOneBy(array('defaut' => true))->getAbreviation();
        }

        $request->setLocale($langue);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(array('onKernelController', 15)),
        );
    }
}