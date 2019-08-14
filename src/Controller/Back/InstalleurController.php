<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-07-17
 * Time: 09:40
 */

namespace App\Controller\Back;


use App\Controller\SEOController;
use App\DataFixtures\AppFixtures;
use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\SEO;
use App\Entity\Utilisateur;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class InstalleurController extends Controller
{
    /**
     * @Route("/installeur/{etape}", name="installeur", requirements={
     *     "etape"="^[1-6]{1,1}$"
     * })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installeur($etape, Request $request, Filesystem $filesystem, ObjectManager $manager, AppFixtures $fixtures){
        $repoConfig = $this->getDoctrine()->getRepository(Configuration::class);

        //Redirection vers étape 1 ou 2 si prob de connexion BDD
        if($etape != 1){
            try {
                $this->getDoctrine()->getConnection()->connect();
            } catch (\Exception $e) {
                return $this->redirectToRoute('installeur', ['etape' => 1]);
            }

            $repoLangue = $this->getDoctrine()->getRepository(Langue::class);

            try {
                $repoConfig->find(1);
            } catch (\Exception $e) {
                return $this->redirectToRoute('installeur', ['etape' => 1]);
            }

            if($repoConfig->find(1) && $repoConfig->find(1)->getInstalle()){
                return $this->redirectToRoute('accueil');
            }
        }

        //Titres des étapes
        $etapes = [
            1 => [
                'titre' => 'Base de données',
                'titreComplet' => 'Configuration de la base de données',
                'icone' => 'database'
            ],
            2 => [
                'titre' => 'Configuration générale',
                'titreComplet' => 'Configuration générale du site',
                'icone' => 'cogs'
            ],
            3 => [
                'titre' => 'Utilisateur',
                'titreComplet' => 'Création du compte administrateur',
                'icone' => 'user'
            ],
            4 => [
                'titre' => 'Thème',
                'titreComplet' => 'Choix du thème',
                'icone' => 'paint-brush'
            ],
            5 => [
                'titre' => 'Contenus',
                'titreComplet' => 'Création de pages',
                'icone' => 'laptop'
            ],
            6 => [
                'titre' => 'Installation terminée',
                'titreComplet' => 'Installation terminée',
                'icone' => 'check-circle'
            ],
        ];

        //Marquage de l'étape active
        foreach($etapes as $numero => $infosEtape){
            $etapes[$numero]['active'] = ($numero == $etape);
        }

        switch($etape){
            case 1: //Configuration de la BDD

                //Vérif étape
                $exception = false;
                try {
                    $this->getDoctrine()->getConnection()->connect();
                } catch (\Exception $e) {
                    $exception = true;
                }

                if(!$exception){
                    try {
                        $repoConfig->find(1);
                    } catch (\Exception $e) {
                        $exception = true;
                    }
                    if(!$exception){
                        if($repoConfig->find(1) && $repoConfig->find(1)->getInstalle()){
                            return $this->redirectToRoute('accueil');
                        }
                    }
                }

                $form = $this->createFormBuilder()
                    ->add('host', TextType::class, [
                        'label' => 'Serveur',
                        'data' => getenv('HOST')
                    ])
                    ->add('database', TextType::class, [
                        'label' => 'Nom de la base de données',
                        'data' => getenv('DATABASE')
                    ])
                    ->add('userdb', TextType::class, [
                        'label' => 'Utilisateur',
                        'data' => getenv('USERDB')
                    ])
                    ->add('password', TextType::class, [
                        'label' => 'Mot de passe',
                        'data' => getenv('PASSWORD')
                    ])
                    ->add('prefixe', TextType::class, [
                        'label' => 'Préfixe',
                        'data' => getenv('PREFIXE')
                    ])
                    ->add('etapeSuivante', SubmitType::class, ['label' => 'Étape suivante'])
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();

                    if($this->testConnexion($request, $data) == 'ok'){
                        $files = glob('../src/Migrations/*');
                        foreach($files as $file){
                            if(is_file($file))
                                unlink($file);
                        }

                        $path = '../config/packages/doctrine_migrations.yaml';

                        $fichier = Yaml::parseFile($path);

                        $fichier['doctrine_migrations']['table_name'] = $form->get('prefixe')->getData()."_migration_versions";

                        $nvFichier = Yaml::dump($fichier);

                        file_put_contents($path, $nvFichier);

                        exec('which php', $php);
                        exec($php[0].' ../bin/console doctrine:migrations:diff --filter-expression=/^'.$_ENV['PREFIXE'].'_/ -nq; '.$php[0].' ../bin/console doctrine:migrations:migrate -nq');

                        return $this->redirectToRoute('installeur', ['etape' => 2]);
                    }
                }

                return $this->render('installeur/1_configBDD.html.twig', ['etapes' => $etapes, 'form' => $form->createView()]);

            case 2: //Configuration du site

                //Vérif étape
                try {
                    $this->getDoctrine()->getConnection()->connect();
                } catch (\Exception $e) {
                    $this->redirectToRoute('installeur', ['etape' => 1]);
                }

                $exception = false;

                try {
                    $repoConfig->find(1);
                } catch (\Exception $e) {
                    $exception = true;
                }

                if(!$exception){
                    if($repoConfig->find(1)){
                        $config = $repoConfig->find(1);
                    }else{
                        $config = new Configuration();
                    }
                }else{
                    $config = new Configuration();
                }

                //Formulaire config
                $form = $this->createFormBuilder($config)
                    ->add('nom', TextType::class, ['label' => 'Nom du site'])
                    ->add('editeur', TextType::class, ['label' => 'Organisme'])
                    ->add('emailContact', EmailType::class, ['label' => 'E-mail de l\'organisme'])
                    ->add('logo', TextType::class, ['label' => 'Logo'])
                    ->add('langueFR', CheckboxType::class, [
                        'label' => 'Définir le français comme langue par défaut',
                        'mapped' => false,
                        'required' => false
                    ])
                    ->add('nomLangue', TextType::class, [
                        'mapped' => false,
                        'required' => false
                    ])
                    ->add('abreviationLangue', TextType::class, [
                        'label' => 'Abréviation',
                        'help' => 'Deux lettres minuscules',
                        'mapped' => false,
                        'required' => false
                    ])
                    ->add('codeLangue', TextType::class, [
                        'label' => 'Code de la langue',
                        'help' => 'Sous la forme xx-XX',
                        'mapped' => false,
                        'required' => false
                    ])
                    ->add('etapeSuivante', SubmitType::class, [
                        'label' => 'Étape suivante'
                    ])
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $config = $form->getData();
                    $config->setEmailMaintenance($config->getEmailContact());

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($config);
                    $em->flush();

                    if($repoLangue->find(1)){
                        $langue = $repoLangue->find(1);
                    }else{
                        $langue = new Langue();
                    }

                    $langue->setActive(1)
                        ->setDefaut(1);

                    if($form->get('langueFR')->getData()){
                        $langue
                            ->setNom('français')
                            ->setAbreviation('fr')
                            ->setCode('fr-FR');
                    }else{//Langue autre que le français
                        $langue
                            ->setNom($form->get('nomLangue')->getData())
                            ->setAbreviation($form->get('abreviationLangue')->getData())
                            ->setCode($form->get('codeLangue')->getData());
                    }

                    $em->persist($langue);
                    $em->flush();

                    return $this->redirectToRoute('installeur', ['etape' => 3]);
                }

                return $this->render('installeur/2_configSite.html.twig', ['etapes' => $etapes, 'form' => $form->createView()]);

            case 3: //Configuration de l'utilisateur admin

                //Vérif étape
                if(!$repoLangue->find(1)){
                    $this->redirectToRoute('installeur', ['etape' => 2]);
                }

                $repoUtilisateur = $this->getDoctrine()->getRepository(Utilisateur::class);
                if($repoUtilisateur->find(1)){
                    $user = $repoUtilisateur->find(1);
                }else{
                    $user = new Utilisateur();
                }

                $form = $this->createFormBuilder($user)
                    ->add('username', TextType::class, ['label' => 'Identifiant'])
                    ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
                    ->add('plainPassword', TextType::class, ['label' => 'Mot de passe'])
                    ->add('etapeSuivante', SubmitType::class, ['label' => 'Étape suivante'])
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $user = $form->getData();

                    $em = $this->getDoctrine()->getManager();

                    $user->setEnabled(1);
                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute('installeur', ['etape' => 4]);
                }

                return $this->render('installeur/3_configUtilisateur.html.twig', ['etapes' => $etapes, 'form' => $form->createView()]);

            case 4: //Choix du thème

                //Vérif étape
                $repoUtilisateur = $this->getDoctrine()->getRepository(Utilisateur::class);
                if(!$repoUtilisateur->find(1)){
                    $this->redirectToRoute('installeur', ['etape' => 3]);
                }

                $themes = ThemeController::listeThemes();

                $config = $repoConfig->find(1);

                $form = $this->createFormBuilder($config)
                    ->add('theme', HiddenType::class)
                    ->add('etapeSuivante', SubmitType::class, ['label' => 'Étape suivante'])
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    //Enregistrement du thème
                    $config = $form->getData();

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($config);
                    $em->flush();

                    $theme = $form->get('theme')->getData();
                    $linkfileP = $this->getParameter('kernel.project_dir').'/public/theme';
                    $linkfileT = $this->getParameter('kernel.project_dir').'/themes/';
                    ThemeController::changementTheme($theme, $linkfileP, $linkfileT, $filesystem);

                    //Création de contenus
                    $fixtures->load($manager, true);

                    return $this->redirectToRoute('installeur', ['etape' => 5]);
                }

                return $this->render('installeur/4_choixTheme.html.twig', ['etapes' => $etapes, 'form' => $form->createView(), 'themes' => $themes]);

            case 5: //Création des contenus

                //Vérif étape
                if(!$repoConfig->find(1)->getTheme()){
                    $this->redirectToRoute('installeur', ['etape' => 4]);
                }

                $repoMenuPage = $this->getDoctrine()->getRepository(MenuPage::class);

                $menusPagesHeader = $repoMenuPage->findBy(['menu' => 1], ['position' => 'ASC']);
                $pagesHeader = [];
                foreach($menusPagesHeader as $menuPage){
                    $pagesHeader[] = $menuPage->getPage()->getTitre();
                }

                $menusPagesFooter = $repoMenuPage->findBy(['menu' => 2], ['position' => 'ASC']);
                $pagesFooter = [];
                foreach($menusPagesFooter as $menuPage){
                    $pagesFooter[] = $menuPage->getPage()->getTitre();
                }

                return $this->render('installeur/5_creationContenus.html.twig', ['etapes' => $etapes, 'pagesHeader' => $pagesHeader, 'pagesFooter' => $pagesFooter]);

            case 6: //Installation terminée

                //Vérif étape
                if(!$repoConfig->find(1)->getTheme()){
                    $this->redirectToRoute('installeur', ['etape' => 5]);
                }

                $em = $this->getDoctrine()->getManager();
                $config = $repoConfig->find(1);

                $config->setInstalle(1);
                $em->persist($config);
                $em->flush();

                return $this->render('installeur/6_validation.html.twig', ['etapes' => $etapes]);
        }
    }

    /**
     * @Route("/installeur/0", name="installeurTestConnexion")
     * @param Request $request
     * @return @return bool|Response
     */
    public function testConnexion(Request $request, $data = null){
        if(!$request->isXmlHttpRequest()){
            $repoConfig = $this->getDoctrine()->getRepository(Configuration::class);

            $exception = false;

            try {
                $repoConfig->find(1);
            } catch (\Exception $e) {
                $exception = true;
            }

            if(!$exception){
                if($repoConfig->find(1) && $repoConfig->find(1)->getInstalle()){
                    return $this->redirectToRoute('accueil');
                }
            }
        }

        $path = '../.env';
        if (file_exists($path)) {
            if ($request->isXmlHttpRequest()) {
                $data = $request->get('form');

                foreach ($data as $donnee) {
                    preg_match_all("/\\[(.*?)\\]/", $donnee['name'], $matches);
                    $cle = $matches[1][0];

                    if ($cle != '_token' && $donnee['value'] != '') {
                        file_put_contents($path, str_replace(
                            strtoupper($cle) . '=' . $_ENV[strtoupper($cle)], strtoupper($cle) . '=' . $donnee['value'], file_get_contents($path)
                        ));
                    }
                }
            }else{
                foreach ($data as $cle => $donnee) {
                    if($donnee != ''){
                        file_put_contents($path, str_replace(
                            strtoupper($cle) . '=' . $_ENV[strtoupper($cle)], strtoupper($cle) . '=' . $donnee, file_get_contents($path)
                        ));
                    }
                }
            }


            try {
                $this->getDoctrine()->getConnection()->connect();
            } catch (\Exception $e) {
                if ($request->isXmlHttpRequest()) {
                    return new Response('echec');
                }else{
                    return 'echec';
                }
            }

            if ($request->isXmlHttpRequest()) {
                return new Response('ok');
            }else{
                return 'ok';
            }
        }
    }

    /**
     * @Route("/installeur/7", name="installeurAjoutPage")
     * @param Request $request
     * @return @return bool|Response
     */
    public function ajoutPage(Request $request, SEOController $seoController){
        if ($request->isXmlHttpRequest()) {
            $titre = $request->get('titre');

            $em = $this->getDoctrine()->getManager();

            $seo = new SEO();
            $seo->setUrl($seoController->slugify($titre))
                ->setMetaTitre($titre)
                ->setMetaDescription($titre);
            $em->persist($seo);

            $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->find(1);
            $langue = $this->getDoctrine()->getRepository(Langue::class)->find(1);
            $date = new \DateTime();

            $page = new Page();
            $page->setTitre($titre)
                ->setTitreMenu($titre)
                ->setAuteur($utilisateur)
                ->setAuteurDerniereModification($utilisateur)
                ->setDateCreation($date)
                ->setDatePublication($date)
                ->setLangue($langue)
                ->setSEO($seo);
            $em->persist($page);

            $idMenu = $request->get('menu');
            $menu = $this->getDoctrine()->getRepository(Menu::class)->find($idMenu);

            $menuPage = new MenuPage();

            $position = $this->getDoctrine()->getRepository(MenuPage::class)->positionMax($idMenu)->getPosition();

            $menuPage->setPosition($position + 1)
                ->setMenu($menu)
                ->setPage($page);
            $em->persist($menuPage);

            $em->flush();

            return new Response('ok');
        }

        return false;
    }
}