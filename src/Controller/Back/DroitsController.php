<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-08-21
 * Time: 09:36
 */

namespace App\Controller\Back;

use App\Entity\Role;
use App\Entity\Utilisateur;
use App\Service\Droits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DroitsController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class DroitsController extends AbstractController
{
    /**
     * @Route("/droits", name="droits")
     * @param Request $request
     * @return bool|Response
     */
    public function listeDroits(Request $request, Droits $droits){
        if(!$droits->checkDroit('droits')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        //Rôles
        $repoRoles = $this->getDoctrine()->getRepository(Role::class);
        $roles = $repoRoles->findAll();

        //Droits
        $droits = Yaml::parsefile('../config/droits.yaml');

        //Création de rôle
        $role = new Role;
        $formCreationRole = $this->createFormBuilder($role)
            ->add('nom', TextType::class, ['label' => false])
            ->add('Enregistrer', SubmitType::class);

        $formCreationRole->get('nom')
            ->addModelTransformer(new CallbackTransformer(
                function ($role) {
                    return substr($role, 5, strlen($role));
                },
                function ($role) {
                    return 'ROLE_'.$role;
                }
            ))
        ;

        $formCreationRole = $formCreationRole->getForm();

        $formCreationRole->handleRequest($request);

        //Enregistrement nouveau rôle
        if ($formCreationRole->isSubmitted() && $formCreationRole->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repoRole = $this->getDoctrine()->getRepository(Role::class);

            $role = $formCreationRole->getData();

            $replica = $repoRole->findOneBy(array('nom' => $role->getNom()));
            if($replica){//Nom unique ?
                $this->addFlash('erreur', 'Le rôle "'.$role->getNom().'" existe déjà');
            }else{
                $em->persist($role);
                $em->flush();

                $this->addFlash('enregistrement', 'Le rôle "'.$role->getNom().'" a été créé');
            }

            return $this->redirectToRoute('droits');
        }

        //Enregistrement droits
        if($request->isMethod('POST')){
            $em = $this->getDoctrine()->getManager();

            foreach($roles as $role){
                foreach($droits as $categorie){
                    foreach($categorie as $droit => $label){
                        $droitsRole = $role->getDroits();
                        if(key_exists($droit, $_POST)){
                            $droitsRole[$droit] = in_array($role->getNom(), $_POST[$droit]);
                        }else{//Droit accordé à aucun rôle
                            $droitsRole[$droit] = false;
                        }
                        $role->setDroits($droitsRole);
                    }
                }
                $em->persist($role);
            }

            $em->flush();

            $this->addFlash('enregistrement', 'Les droits ont été enregistrés');
        }

        $entityConfig = ['name' => 'Droit'];

        return $this->render('back/droits.html.twig', ['roles' => $roles, 'droits' => $droits, 'formCreationRole' => $formCreationRole->createView(), '_entity_config' => $entityConfig]);
    }

    /**
     * @Route("/droits/suppressionRole", name="suppressionRole")
     * @param Request $request
     * @return bool|Response
     */
    public function suppressionRole(Request $request){
        if($request->isXmlHttpRequest()){
            $idRole = $request->get('idRole');

            //Suppression du rôle
            $em = $this->getDoctrine()->getManager();
            $repoRole = $this->getDoctrine()->getRepository(Role::class);
            $role = $repoRole->find($idRole);
            $nomRole = $role->getNom();

            $em->remove($role);

            $this->addFlash('enregistrement', 'Le rôle "'.$nomRole.'" a été supprimé');

            //Suppression du rôle chez les utilisateurs
            $repoUtilisateur = $this->getDoctrine()->getRepository(Utilisateur::class);
            $utilisateurs = $repoUtilisateur->getUtilisateursAvecRole($nomRole);

            foreach($utilisateurs as $utilisateur){
                $utilisateur->removeRole($nomRole);
                $em->persist($utilisateur);
            }

            $em->flush();

            return new Response('ok');
        }

        return false;
    }
}