<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-09-02
 * Time: 14:42
 */

namespace App\Event;


use App\Controller\AccueilController;
use App\Service\Droits;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class AccesBO implements EventSubscriberInterface
{
    private $droits;

    public function __construct(Droits $droits)
    {
        $this->droits = $droits;
    }

    public function verificationAccesBO(ControllerEvent $event)
    {
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