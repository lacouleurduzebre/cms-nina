<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class RolesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Création rôles
        $droits = Yaml::parseFile(getcwd().'/config/droits.yaml');

        $droitsAdmin = [];
        $droitsUtilisateur = [];
        foreach($droits as $categorie){
            foreach($categorie as $droit => $label){
                $droitsAdmin[$droit] = true;
                $droitsUtilisateur[$droit] = ($droit == 'admin');//Seul le droit "admin" est attribué aux utilisateurs
            }
        }

        $roleAdmin = new Role();
        $roleAdmin->setNom('ROLE_ADMIN')
            ->setDroits($droitsAdmin);
        $manager->persist($roleAdmin);

        $roleUtilisateur = new Role();
        $roleUtilisateur->setNom('ROLE_UTILISATEUR')
            ->setDroits($droitsUtilisateur);
        $manager->persist($roleUtilisateur);

        //Ajout du rôle admin à tous les comptes
        $repoUtilisateurs = $manager->getRepository(Utilisateur::class);
        $utilisateurs = $repoUtilisateurs->findAll();
        foreach($utilisateurs as $utilisateur){
            $utilisateur->addRole('ROLE_ADMIN');
            $manager->persist($utilisateur);
        }

        $manager->flush();
    }
}
