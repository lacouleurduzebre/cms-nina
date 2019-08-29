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
use App\Entity\SEO;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setUsername('admin')
            ->setEmail('maintenance@lacouleurduzebre.com')
            ->setPlainPassword('admin')
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
            ->setMaintenance(true);
        $manager->persist($config);

        $date = new \DateTime();

        //Langue : français
        $langue = new Langue();
        $langue->setNom('français')
            ->setAbreviation('fr')
            ->setDefaut(true)
            ->setCode('fr-FR');
        $manager->persist($langue);

        //Page sans titre
        $seo = new SEO();
        $seo->setUrl('page-sans-titre')
            ->setMetaTitre('Page sans titre')
            ->setMetaDescription('Page sans titre');
        $manager->persist($seo);

        $page = new Page();
        $page->setTitre('Page sans titre')
            ->setTitreMenu('Page sans titre')
            ->setAuteur($utilisateur)
            ->setAuteurDerniereModification($utilisateur)
            ->setDateCreation($date)
            ->setDatePublication($date)
            ->setLangue($langue)
            ->setSEO($seo);
        $manager->persist($page);

        $langue->setPageAccueil($page);
        $manager->persist($langue);

        //Menu principal
        $menu = new Menu();
        $menu->setNom('Menu principal')
            ->setLangue($langue)
            ->setDefaut(true);
        $manager->persist($menu);

        $menuPage = new MenuPage();
        $menuPage->setPosition(0)
            ->setMenu($menu)
            ->setPage($page);
        $manager->persist($menuPage);

        //Menu footer
        $menuFooter = new Menu();
        $menuFooter->setNom('Menu du pied de page')
            ->setLangue($langue)
            ->setDefaut(false);
        $manager->persist($menuFooter);

        //Premier flush pour obtenir les id des menus
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
