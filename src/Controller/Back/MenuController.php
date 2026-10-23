<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 06/12/2017
 * Time: 10:18
 */

namespace App\Controller\Back;


use App\Controller\SEOController;
use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Menu;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\SEOPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class MenuController
 * @package App\Controller\Back
 */
#[Route(path: '/admin')]
class MenuController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/menu/enregistrer', name: 'enregistrerMenu')]
    public function enregistrerAction(Request $request){
        if($request->isXmlHttpRequest()){
            $arbo = $request->request->all('arbo');
            $repositoryMenuPage = $this->doctrine->getRepository(MenuPage::class);
            $em = $this->doctrine->getManager();

            //$item[0] menuPage
            //$item[1] page
            //$item[2] position
            //$item[3] parent
            //$item[4] menu
            foreach($arbo as $item){
                $menuPage = $repositoryMenuPage->find($item[0]);

                $repositoryMenu = $this->doctrine->getRepository(Menu::class);
                $repositoryPage = $this->doctrine->getRepository(Page::class);
                $repositoryMenuPage = $this->doctrine->getRepository(MenuPage::class);

                if($item[4] == 0){
                    $menuPage->setMenu(null);
                }else{
                    $menu = $repositoryMenu->findOneBy(array('id'=>$item[4]));
                    $menuPage->setMenu($menu);
                }

                $page = $repositoryPage->find($item[1]);
                $parent = $repositoryMenuPage->find($item[3]);

                $menuPage->setPosition($item[2])->setPage($page)->setParent($parent);

                $em->persist($menuPage);
            };

            $em->flush();

            return new Response('ok');
        };

        return false;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/menu/ajouter', name: 'ajouterPageEnfant')]
    public function ajouterPageEnfantAction(Request $request, UserInterface $user){
        if($request->isXmlHttpRequest()){
            //Infos pop-up
            $data = $request->request->all('donneesFormulaire');
            $titre = $data[array_search('ajoutPage-titre', array_column($data, 'name'))]['value'];
            $titreMenu = $data[array_search('ajoutPage-titreMenu', array_column($data, 'name'))]['value'];
            $url = SEOController::slugify($titre);
            //Infos pop-up

            $em = $this->doctrine->getManager();
            $repoSEO = $em->getRepository(SEOPage::class);
            $repoMenu = $em->getRepository(Menu::class);
            $repoMenuPage = $em->getRepository(MenuPage::class);
            $repoLangue = $em->getRepository(Langue::class);
            $config = $em->getRepository(Configuration::class)->find(1);

            //Création page
            $page = new Page();

            while($repoSEO->findOneBy(array('url' => $url))){
                $url = $url.'-copie';
            }

            $page->setTitre($titre);
            $page->setTitreMenu($titreMenu);
            $page->setAuteur($user);
            $page->setAuteurDerniereModification($user);
            $page->setAffichageCommentaires($config->getAffichageCommentaires());
            $page->setAffichageDatePublication($config->getAffichageDatePublication());
            $page->setAffichageAuteur($config->getAffichageAuteur());
            $SEO = new SEOPage();
            $SEO->setMetaTitre($titre)->setUrl($url)->setMetaDescription($titre);
            $page->setSeo($SEO);

            $idLangue = $data[array_search('ajoutPage-idLangue', array_column($data, 'name'))]['value'];
            $langue = $repoLangue->find($idLangue);
            $page->setLangue($langue);

            $em->persist($page);
            $em->persist($SEO);
            $em->flush();

            $idPage = $page->getId();

            //Création menuPage
            $menuPage = new MenuPage();

            $idParent = $data[array_search('ajoutPage-idParent', array_column($data, 'name'))]['value'];
            $parent = $repoMenuPage->find($idParent);

            $idMenu = $data[array_search('ajoutPage-idMenu', array_column($data, 'name'))]['value'];
            $menu = $repoMenu->find($idMenu);

            $menuPage->setPosition(0)->setPage($page)->setParent($parent)->setMenu($menu);

            $em->persist($menuPage);
            $em->flush();

            $idMenuPage = $menuPage->getId();

            $data = $idPage.'*'.$idMenuPage;
            return new Response($data);
        };

        return false;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/menu/alias', name: 'creerAlias')]
    public function creerAlias(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->doctrine->getManager();
            $repoMenu = $em->getRepository(Menu::class);
            $repoPage = $em->getRepository(Page::class);
            $repoMenuPage = $em->getRepository(MenuPage::class);

            //Page copiée à partir des pages orphelines ?
            $idAncienMenuComplet = $request->request->get('idAncienMenuComplet');
            if($idAncienMenuComplet == 'menu-0'){//Si oui on modifie menuPage
                $idAnciendMenuPage = $request->request->get('idAnciendMenuPage');
                $menuPage = $repoMenuPage->find($idAnciendMenuPage);
                $menuPage->setMenu(null);
            }else{//Si non on créé un nouveau menuPage
                $menuPage = new MenuPage();

                $idPage = $request->request->get('idPage');
                $page = $repoPage->find($idPage);

                $idMenu = $request->request->get('idMenu');
                $menu = $repoMenu->find($idMenu);

                $menuPage->setPosition(0)->setPage($page)->setParent(null)->setMenu($menu);
            }

            $em->persist($menuPage);

            $em->flush();

            $idMenuPage = $menuPage->getId();

            $data = $idMenuPage;
            return new Response($data);
        };

        return false;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/menu/voirPage', name: 'urlPage')]
    public function urlPageAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->doctrine->getManager();
            $repoPage = $em->getRepository(Page::class);

            $idPage = $request->request->get('idPage');
            $page = $repoPage->find($idPage);
            $locale = $page->getLangue()->getAbreviation();
            $url = $page->getSeo()->getUrl();

            return new Response($locale.'/'.$url);
        };

        return false;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/menu/definirPageAccueil', name: 'definirPageAccueil')]
    public function definirPageAccueilAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->doctrine->getManager();

            //Page
            $repoPage = $em->getRepository(Page::class);
            $idPage = $request->request->get('idPage');
            $page = $repoPage->find($idPage);
            $titrePage = $page->getTitre();

            //Langue
            $repoLangue = $em->getRepository(Langue::class);
            if(isset($_COOKIE['langueArbo'])){
                $langueArbo = $_COOKIE['langueArbo'];
            }else{
                $langueArbo = $repoLangue->findOneBy(array('defaut' => 1));
            }
            $langue = $repoLangue->find($langueArbo);

            $langue->setPageAccueil($page);

            $em->persist($langue);
            $em->flush();

            return new Response($titrePage);
        };

        return false;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/menu/changerMenuPrincipal', name: 'changerMenuPrincipal')]
    public function changerMenuPrincipalAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->doctrine->getManager();

            $id = $request->request->get('id');

            $repoMenu = $em->getRepository(Menu::class);
            $menu = $repoMenu->find($id);

            $valeur = $request->request->get('valeur');

            if($valeur == false){
                $menu->setDefaut(0);

                $em->persist($menu);
                $em->flush();

                return new Response('ok');
            }else{
                $langue = $menu->getLangue();

                $menuPrincipal = $repoMenu->findOneBy(array('langue'=>$langue, 'defaut'=>'1'));

                if(!$menuPrincipal){
                    $menu->setDefaut(1);
                    $em->persist($menu);
                    $em->flush();

                    return new Response('ok');
                }else{
                    $menuPrincipal->setDefaut(0);
                    $em->persist($menuPrincipal);
                    $menu->setDefaut(1);
                    $em->persist($menu);
                    $em->flush();

                    return new Response($menuPrincipal->getId());
                }
            }
        };

        return false;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/menu/filtreArbo', name: 'filtreArbo')]
    public function filtreArbo(Request $request){
        if($request->isXmlHttpRequest()){
            $langue = $request->request->get('langue');
            $recherche = $request->request->get('recherche');

            $repoPage = $this->doctrine->getRepository(Page::class);
            $pages = $repoPage->titreMenuLike($langue, $recherche);

            $tpl = $this->render('/back/menu/pagesFiltrees.html.twig', array('pages' => $pages))->getContent();

            return new Response($tpl);
        }
        return false;
    }
}