<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-09-02
 * Time: 14:42
 */

namespace App\Event;


use App\Controller\AccueilController;
use App\Entity\Configuration;
use App\Service\Droits;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class AccesBO implements EventSubscriberInterface
{
    private $droits;

    public function __construct(Droits $droits, RegistryInterface $doctrine)
    {
        $this->droits = $droits;
        $this->doctrine = $doctrine;
    }

    public function verificationAccesBO(ControllerEvent $event)
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
            if(!$this->droits->checkDroit('admin')){
                $event->setController(function () {
                    return new RedirectResponse('/');
                });
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(array('verificationAccesBO', 10)),
        );
    }
}