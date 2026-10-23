<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use App\Entity\Langue;
use App\Entity\Page;
use App\Entity\Utilisateur;
use App\Service\AdminUrl;
use App\Service\MenuAdmin;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private $doctrine;
    private $menuAdmin;
    private $adminUrl;

    public function __construct(ManagerRegistry $doctrine, MenuAdmin $menuAdmin, AdminUrl $adminUrl)
    {
        $this->doctrine = $doctrine;
        $this->menuAdmin = $menuAdmin;
        $this->adminUrl = $adminUrl;
    }

    //Tableau de bord (route "admin" définie par #[AdminDashboard])
    public function index(): Response
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        //Anciennes URL d'EasyAdmin 2 (/admin/?entity=...&action=...), encore construites par le JS
        if (null !== $url = $this->adminUrl->depuisAncienneUrl($request)) {
            return $this->redirect($url);
        }

        $em = $this->doctrine->getManager();
        $repositoryLangue = $em->getRepository(Langue::class);
        $repositoryPage = $em->getRepository(Page::class);
        $user = $this->getUser();
        $locale = $request->getLocale();
        $langue = $repositoryLangue->findOneBy(array('abreviation' => $locale));

        $blocsUser = $user->getBlocsTableauDeBord();
        $blocs = [];

        if($blocsUser){
            /* Dernières pages publiées */
            if(in_array('dernieresPages', $blocsUser)){
                $dernieresPages = $repositoryPage->pagesPubliees($langue, 5);
                $blocs['dernieresPages'] = $dernieresPages;
            }

            /* Derniers commentaires en attente de validation */
            if(in_array('derniersCommentaires', $blocsUser)){
                $repositoryCommentaire = $em->getRepository(Commentaire::class);
                $derniersCommentaires = $repositoryCommentaire->commentairesNonValides();
                $blocs['derniersCommentaires'] = $derniersCommentaires;
            }

            /* Nombre de pages */
            if(in_array('nombreDePages', $blocsUser)){
                //Nombre total
                $nombreTotal = $repositoryPage->nombreTotal();
                $blocs['nombreDePages']['nombreTotal'] = $nombreTotal;

                //Nombre total de pages publiées
                $nombreTotalPagesPubliees = $repositoryPage->nombrePagesPubliees();
                $blocs['nombreDePages']['publiees']['nombreTotal'] = $nombreTotalPagesPubliees;

                //Nombre de pages dans chaque langue
                $langues = $repositoryLangue->findAll();
                foreach($langues as $langue){
                    $nombrePagesPublieesLangue = $repositoryPage->nombrePagesPubliees($langue);
                    $blocs['nombreDePages']['publiees']['parLangue'][$langue->getNom()] = $nombrePagesPublieesLangue;
                }
            }

            /* Liste des utilisateurs */
            if(in_array('derniersInscrits', $blocsUser)){
                $repositoryUtilisateur = $em->getRepository(Utilisateur::class);
                $utilisateurs = $repositoryUtilisateur->triDateDerniereConnexion();
                $blocs['derniersInscrits'] = $utilisateurs;
            }

            /* Nouvelles de version */
            if(in_array('logVersion', $blocsUser)){
                $blocs['logVersion'] = 'ok';
            }

            /* Aperçu du référencement */
            if(in_array('referencement', $blocsUser)){
                $pagesHorsCorbeille = $repositoryPage->findBy(['corbeille' => false]);

                $total = count($pagesHorsCorbeille);
                $scoreMetaTitre = 0;
                $scoreUrl = 0;
                $scoreMetaDescription = 0;

                foreach($pagesHorsCorbeille as $page){
                    $seo = $page->getSEO();

                    if(strlen($seo->getMetaTitre()) > ((65/3)*2)){
                        $scoreMetaTitre++;
                    }
                    if(strlen($seo->getUrl()) > ((75/3)*2)){
                        $scoreUrl++;
                    }
                    if(strlen($seo->getMetaDescription()) > ((150/3)*2)){
                        $scoreMetaDescription++;
                    }
                }

                $scoreTotal = ($scoreMetaTitre + $scoreUrl + $scoreMetaDescription)/3;

                $blocs['referencement'] = ['total' => $total, 'scoreTotal' => $scoreTotal, 'scoreMetaTitre' => $scoreMetaTitre, 'scoreUrl' => $scoreUrl, 'scoreMetaDescription' => $scoreMetaDescription];

                /* Pages sans contenus */
                if(in_array('pagesSansContenu', $blocsUser)){
                    $blocs['pagesSansContenu'] = $repositoryPage->pagesSansContenu();
                }
            }
        }

        //Version
        exec('which git', $git);
        if(!empty($git)){
            exec('cd ..; '.$git[0].' log -1', $versionBrut);

            $version = [];
            foreach($versionBrut as $line){
                if (!empty($line)) {
                    // Commit
                    if (strpos($line, 'commit') !== false) {
                        $hash = explode(' ', $line);
                        $hash = trim(end($hash));
                        $version = [
                            'message' => ''
                        ];
                        $version['numero'] = $hash;
                    } // Author
                    else if (strpos($line, 'Author') !== false) {
                        $author = explode(':', $line);
                        $author = trim(end($author));
                        $version['auteur'] = $author;
                    } // Date
                    else if (strpos($line, 'Date') !== false) {
                        $date = explode(':', $line, 2);
                        $date = trim(end($date));
                        $version['date'] = date('d/m/Y', strtotime($date));
                    } // Message
                    else {
                        if(!key_exists('message', $version)){
                            $version['message'] = '';
                        }
                        $version['message'] .= $line . " ";
                    }
                }
            }

            //Màj dispo ?
            exec($git[0].' remote update');
            $versionLocale = exec($git[0].' rev-parse master');
            $versionEnLigne = exec($git[0].' rev-parse origin/master');

            if($versionLocale != $versionEnLigne){
                $majDispo = true;
            }else{
                $majDispo = false;
            }
        }

        return $this->render('back/tableauDeBord.html.twig', array(
            'user' => $user,
            'blocs' => $blocs,
            'version' => $version ?? false,
            'majDispo' => $majDispo ?? false
        ));
    }

    /**
     * Ancienne URL du tableau de bord
     * @Route("/admin/accueil", name="tableauDeBord")
     */
    public function tableauDeBord(): Response
    {
        return $this->redirect($this->adminUrl->tableauDeBord());
    }

    /**
     * @Route("/admin/mediatheque", name="mediatheque")
     */
    public function mediatheque(): Response
    {
        return $this->render('back/mediatheque.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/assets/img/logoNinaBlanc.png" alt="CMS Nina 3">')
            ->setFaviconPath('assets/img/favicon.ico')
            ->setTranslationDomain('messages');
    }

    public function configureMenuItems(): iterable
    {
        foreach ($this->menuAdmin->getMenu() as $item) {
            if (!$item['enfants']) {
                yield $this->lienMenu($item);
                continue;
            }

            $avecSousMenus = (bool) array_filter($item['enfants'], function ($enfant) { return (bool) $enfant['enfants']; });
            if (!$avecSousMenus) {
                yield MenuItem::subMenu($item['label'], $item['icon'])->setSubItems(array_map([$this, 'lienMenu'], $item['enfants']));
                continue;
            }

            //Un niveau de trop pour EasyAdmin : le premier niveau devient une section
            yield MenuItem::section($item['label'], $item['icon']);
            foreach ($item['enfants'] as $enfant) {
                yield $enfant['enfants']
                    ? MenuItem::subMenu($enfant['label'], $enfant['icon'])->setSubItems(array_map([$this, 'lienMenu'], $enfant['enfants']))
                    : $this->lienMenu($enfant);
            }
        }
    }

    private function lienMenu(array $item): MenuItemInterface
    {
        return MenuItem::linkToUrl($item['label'], $item['icon'], $item['url'])->setCssClass($item['css_class']);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToUrl('Mon profil', 'fas fa-user', $this->adminUrl->crud('Utilisateur', 'edit', $user->getId())),
            ]);

        if ($user instanceof Utilisateur && $user->getImageProfil()) {
            $userMenu->setAvatarUrl($user->getImageProfil());
        }

        return $userMenu;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateFormat('dd/MM/yyyy')
            ->setTimeFormat('HH:mm')
            ->setDateTimeFormat('dd/MM/yyyy HH:mm')
            ->setPaginatorPageSize(30)
            //Actions affichées sous forme d'icônes dans les listes, comme avec EasyAdmin 2
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'layout' => 'back/layout.html.twig',
                'flash_messages' => 'back/flash_messages.html.twig',
            ]);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fas fa-pencil-alt')->setLabel(false)->setHtmlAttributes(['title' => 'Éditer']);
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fas fa-trash')->setLabel(false)->setHtmlAttributes(['title' => 'Supprimer']);
            })
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fas fa-plus')->setLabel('Ajouter');
            });
    }

    public function configureAssets(): Assets
    {
        $assets = Assets::new();

        foreach (['assets/js/featherlight.min.css', 'assets/css/select2-bootstrap.min.css', 'assets/css/knacss.min.css', 'assets/css/back.min.css', 'assets/js/jstree/themes/style.css', 'assets/js/fancybox/jquery.fancybox.min.css'] as $css) {
            $assets->addCssFile($css);
        }

        //Bibliothèques chargées dans le <head> (jQuery et ses plugins, routing JS, TinyMCE...)
        foreach (['assets/js/jquery-3.2.1.min.js', 'assets/js/easyadmin/easyadmin-all.min.js', 'assets/js/fosjsrouting/js/router.js', 'assets/js/fosjsrouting/js/fos_js_routes.js', 'assets/js/tinymce/tinymce.min.js', 'assets/js/fancybox/jquery.fancybox.min.js'] as $js) {
            $assets->addJsFile($js);
        }

        return $assets;
    }
}
