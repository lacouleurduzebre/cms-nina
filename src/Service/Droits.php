<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-21
 * Time: 13:29
 */

namespace App\Service;


use App\Entity\Role;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Droits
{
    public function __construct(RequestStack $request, RegistryInterface $doctrine, TokenStorageInterface $token, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->request = $request;
        $this->doctrine = $doctrine;
        $this->token = $token;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function checkDroit($droit){
        $user = $this->token->getToken()->getUser();

        if($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                return true;
            }

            $repoRole = $this->doctrine->getRepository(Role::class);
            $roles = $user->getRoles();

            foreach($roles as $role){
                $role = $repoRole->findOneBy(array('nom' => $role));
                if($role){
                    $droits = $role->getDroits();
                    if((isset($droits[$droit]) and $droits[$droit]))
                        return true;
                }
            }
        }

        return false;
    }
}