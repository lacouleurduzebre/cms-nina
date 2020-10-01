<?php

namespace App\DataFixtures;

use App\Entity\Bloc;
use App\Entity\Configuration;
use App\Entity\GroupeBlocs;
use App\Entity\Langue;
use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\Region;
use App\Entity\SEOPage;
use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager, $installeur = false)
    {
        $date = new \DateTime();

        //Rôles
        if($installeur){
            $droits = Yaml::parseFile('../config/droits.yaml');
        }else{
            $droits = Yaml::parseFile(getcwd().'/config/droits.yaml');
        }

        $droitsAdmin = [];
        $droitsUtilisateur = [];
        foreach($droits as $categorie){
            foreach($categorie as $droit => $label){
                $droitsAdmin[$droit] = true;
                $droitsUtilisateur[$droit] = ($droit == 'admin');
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

        if(!$installeur){
            //Admin
            $utilisateur = new Utilisateur();
            $utilisateur->setUsername('admin')
                ->setEmail('maintenance@lacouleurduzebre.com')
                ->setPlainPassword('admin')
                ->addRole('ROLE_ADMIN')
                ->setEnabled(true);
            $manager->persist($utilisateur);

            //Configuration générale
            $config = new Configuration();
            $config->setNom('Nouveau site')
                ->setLogo('/theme/img/logo.png')
                ->setEmailContact('maintenance@lacouleurduzebre.com')
                ->setEmailMaintenance('maintenance@lacouleurduzebre.com')
                ->setEditeur('la couleur du Zèbre')
                ->setTheme('nina')
                ->setMaintenance(false)
                ->setInstalle(true);
            $manager->persist($config);

            //Langue : français
            $langue = new Langue();
            $langue->setNom('français')
                ->setAbreviation('fr')
                ->setDefaut(true)
                ->setCode('fr-FR');
            $manager->persist($langue);

            $manager->flush();
        }else{
            $repoConfig = $manager->getRepository(Configuration::class);
            $config = $repoConfig->find(1);
        }

        $repoUtilisateur = $manager->getRepository(Utilisateur::class);
        $utilisateur = $repoUtilisateur->find(1);

        $repoLangue = $manager->getRepository(Langue::class);
        $langue = $repoLangue->find(1);

        $repoSEOPage = $manager->getRepository(SEOPage::class);

        //Accueil
        if(!$repoSEOPage->findOneBy(array('url' => 'accueil'))){
            $seo = new SEOPage();
            $seo->setUrl('accueil')
                ->setMetaTitre('Accueil')
                ->setMetaDescription('Accueil');
            $manager->persist($seo);

            $page = new Page();
            $page->setTitre('Accueil')
                ->setTitreMenu('Accueil')
                ->setAuteur($utilisateur)
                ->setAuteurDerniereModification($utilisateur)
                ->setDateCreation($date)
                ->setDatePublication($date)
                ->setLangue($langue)
                ->setSEO($seo);
            $manager->persist($page);

            $langue->setPageAccueil($page);
            $manager->persist($langue);
        }

        //Page mentions légales
        if(!$repoSEOPage->findOneBy(array('url' => 'mentions-legales'))) {
            $seoML = new SEOPage();
            $seoML->setUrl('mentions-legales')
                ->setMetaTitre('Mentions légales')
                ->setMetaDescription('Mentions légales');
            $manager->persist($seoML);

            $pageML = new Page();
            $pageML->setTitre('Mentions légales')
                ->setTitreMenu('Mentions légales')
                ->setAuteur($utilisateur)
                ->setAuteurDerniereModification($utilisateur)
                ->setDateCreation($date)
                ->setDatePublication($date)
                ->setLangue($langue)
                ->setSEO($seoML);
            $manager->persist($pageML);

            $config->setBandeauCookies(true)
                ->setPageCookies($pageML);
            $manager->persist($config);
        }

        //Menu principal
        $menu = new Menu();
        $menu->setNom('Menu principal')
            ->setLangue($langue)
            ->setDefaut(true)
            ->setPriorite(1);
        $manager->persist($menu);

        if(!$repoSEOPage->findOneBy(array('url' => 'accueil'))){
            $menuPage = new MenuPage();
            $menuPage->setPosition(0)
                ->setMenu($menu)
                ->setPage($page);
            $manager->persist($menuPage);
        }

        //Menu footer
        $menuFooter = new Menu();
        $menuFooter->setNom('Menu du pied de page')
            ->setLangue($langue)
            ->setDefaut(false)
            ->setPriorite(2);
        $manager->persist($menuFooter);

        if(!$repoSEOPage->findOneBy(array('url' => 'mentions-legales'))){
            $menuPageFooter = new MenuPage();
            $menuPageFooter->setPosition(0)
                ->setMenu($menuFooter)
                ->setPage($pageML);
            $manager->persist($menuPageFooter);
        }

        //Flush pour obtenir les id des menus
        $manager->flush();

        //Régions header, contenu et footer
        $header = new Region();
        $header->setNom('En-tête')
            ->setIdentifiant('header')
            ->setPosition(0);
        $manager->persist($header);

        $contenu = new Region();
        $contenu->setNom('Corps de la page')
            ->setIdentifiant('contenu')
            ->setPosition(1);
        $manager->persist($contenu);

        $footer = new Region();
        $footer->setNom('Pied de page')
            ->setIdentifiant('footer')
            ->setPosition(2);
        $manager->persist($footer);

        //Bloc menu dans le header
        $groupeBlocHeader = new GroupeBlocs();
        $groupeBlocHeader->setNom('Header')
            ->setLangue($langue)
            ->setRegion($header)
            ->setIdentifiant('header')
            ->setPosition(0);
        $manager->persist($groupeBlocHeader);

        $contenuBlocMenuPrincipal = [];
        $contenuBlocMenuPrincipal['menu'] = $menu->getId();

        $blocMenuPrincipal = new Bloc();
        $blocMenuPrincipal->setType('Menu')
            ->setPosition(1)
            ->setGroupeBlocs($groupeBlocHeader)
            ->setContenu($contenuBlocMenuPrincipal);
        $manager->persist($blocMenuPrincipal);

        //Bloc logo dans le header
        $contenuBlocLogo = [];
        $contenuBlocLogo['logo'] = [1];
        $contenuBlocLogo['nom'] = [0];

        $blocLogo = new Bloc();
        $blocLogo->setType('LogoSite')
            ->setPosition(0)
            ->setGroupeBlocs($groupeBlocHeader)
            ->setContenu($contenuBlocLogo);
        $manager->persist($blocLogo);

        //Bloc menu dans le footer
        $groupeBlocFooter = new GroupeBlocs();
        $groupeBlocFooter->setNom('Footer')
            ->setLangue($langue)
            ->setRegion($footer)
            ->setIdentifiant('footer')
            ->setPosition(0);
        $manager->persist($groupeBlocFooter);

        $contenuBlocMenuFooter = [];
        $contenuBlocMenuFooter['menu'] = $menuFooter->getId();

        $blocMenuFooter = new Bloc();
        $blocMenuFooter->setType('Menu')
            ->setPosition(0)
            ->setGroupeBlocs($groupeBlocFooter)
            ->setContenu($contenuBlocMenuFooter);
        $manager->persist($blocMenuFooter);

        $manager->flush();
    }
}
