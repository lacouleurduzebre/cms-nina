<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 19/02/2018
 * Time: 10:28
 */

namespace App\Controller\Back;

use App\Entity\BlocAnnexe;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Entity\Langue;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\TypeCategorie;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class AdminController extends BaseAdminController
{
    protected function editAction($listeBlocs = false)
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if($listeBlocs){
            $blocs = $this->listeBlocs('contenu');
            $blocsAnnexes = $this->listeBlocs('annexe', $entity);
        }

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === \mb_strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(\sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            // cast to integer instead of string to avoid sending empty responses for 'false'
            return new Response((int) $newValue);
        }

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->executeDynamicMethod('create<EntityName>EditForm', [$entity, $fields]);
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, ['entity' => $entity]);
            $this->executeDynamicMethod('update<EntityName>Entity', [$entity, $editForm]);
            $this->dispatch(EasyAdminEvents::POST_UPDATE, ['entity' => $entity]);

            $tpl = $this->render('back/messageEnregistrement.html.twig', ['entite' => $entity])->getContent();
            return new Response($tpl);
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        $parameters = [
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'blocs' => isset($blocs) ? $blocs : null,
            'blocsAnnexes' => isset($blocsAnnexes) ? $blocsAnnexes : null
        ];

        return $this->executeDynamicMethod('render<EntityName>Template', ['edit', $this->entity['templates']['edit'], $parameters]);
    }

    protected function newAction($listeBlocs = false)
    {
        $this->dispatch(EasyAdminEvents::PRE_NEW);

        $entity = $this->executeDynamicMethod('createNew<EntityName>Entity');

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        if($listeBlocs){
            $blocs = $this->listeBlocs('contenu');
            $blocsAnnexes = $this->listeBlocs('annexe');
        }

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', [$entity, $fields]);

        $newForm->handleRequest($this->request);
        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_PERSIST, ['entity' => $entity]);
            $this->executeDynamicMethod('persist<EntityName>Entity', [$entity, $newForm]);
            $this->dispatch(EasyAdminEvents::POST_PERSIST, ['entity' => $entity]);

            $tpl = $this->render('back/messageEnregistrement.html.twig', ['entite' => $entity])->getContent();
            return new Response($tpl);
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, [
            'entity_fields' => $fields,
            'form' => $newForm,
            'entity' => $entity,
        ]);

        $parameters = [
            'form' => $newForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'blocs' => isset($blocs) ? $blocs : null,
            'blocsAnnexes' => isset($blocsAnnexes) ? $blocsAnnexes : null
        ];

        return $this->executeDynamicMethod('render<EntityName>Template', ['new', $this->entity['templates']['new'], $parameters]);
    }

    protected function listeBlocs($typeBloc, $entity = null){
        $types = scandir('../src/Blocs');
        $types = array_combine(array_values($types), array_values($types));
        unset($types["."]);
        unset($types[".."]);
        unset($types["configBlocs.yaml"]);
        $config = Yaml::parseFile('../src/Blocs/configBlocs.yaml');

        $repoBlocAnnexe = $this->getDoctrine()->getRepository(BlocAnnexe::class);

        $blocs = [];
        foreach($types as $type){
            if(file_exists('../src/Blocs/'.$type.'/infos.yaml')){
                $infos = Yaml::parseFile('../src/Blocs/'.$type.'/infos.yaml');
                if($config[$type]['actif'] == 'oui' && $infos['type'] == $typeBloc){
                    $blocs[$type] = $infos;
                    $blocs[$type]['priorite'] = $config[$type]['priorite'];
                    if($typeBloc == 'annexe' && $entity != null){
                        $blocAnnexe = $repoBlocAnnexe->findOneBy(array('page' => $entity, 'type' => $type));
                        if($blocAnnexe){
                            $blocs[$type]['disabled'] = true;
                        }
                    }
                }
            }
        }
        uasort($blocs, array($this,'comparaison'));

        return $blocs;
    }

    private static function comparaison($a, $b) {
        if ($a['priorite'] == $b['priorite']) {
            return 0;
        }
        return ($a['priorite'] < $b['priorite']) ? -1 : 1;
    }

    /**
     * @return RedirectResponse
     */
    protected function redirectToReferrer()
    {
        $refererAction = $this->request->query->get('action');

        // from new|edit action, redirect to edit if possible
        if (in_array($refererAction, array('new', 'edit')) && $this->isActionAllowed('edit')) {
            return $this->redirectToRoute('easyadmin', array(
                'action' => 'edit',
                'entity' => $this->entity['name'],
                'menuIndex' => $this->request->query->get('menuIndex'),
                'submenuIndex' => $this->request->query->get('submenuIndex'),
                'id' => ('new' === $refererAction)
                    ? PropertyAccess::createPropertyAccessor()->getValue($this->request->attributes->get('easyadmin')['item'], $this->entity['primary_key_field_name'])
                    : $this->request->query->get('id'),
            ));
        }

        return parent::redirectToReferrer();
    }
    /**
     * @Route("/accueil", name="tableauDeBord")
     */
    public function tableauDeBordAction(Request $request){
        $em = $this->getDoctrine()->getManager();
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
                $dernieresPages = $em->getRepository(Page::class)->pagesPubliees($langue, 5);
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

            /* Liste des catégories */
            if(in_array('listeCategories', $blocsUser)){
                $repositoryTypeCategorie = $em->getRepository(TypeCategorie::class);
                $typeCategories = $repositoryTypeCategorie->findAll();
                $blocs['listeCategories'] = $typeCategories;
            }

            /* Liste des derniers inscrits */
            if(in_array('derniersInscrits', $blocsUser)){
                $repositoryUtilisateur = $em->getRepository(Utilisateur::class);
                $utilisateurs = $repositoryUtilisateur->findBy(array(), array('id' => 'ASC'), 5);
                $blocs['derniersInscrits'] = $utilisateurs;
            }

            /* Nouvelles de version */
            if(in_array('logVersion', $blocsUser)){
                $blocs['logVersion'] = 'ok';
            }
        }

        //Version
        exec('which git', $git);
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

        return $this->render('back/tableauDeBord.html.twig', array(
            'user' => $user,
            'blocs' => $blocs,
            'version' => $version,
            'majDispo' => $majDispo
        ));
    }

    /**
     * @Route("/mediatheque", name="mediatheque")
     */
    public function mediathequeAction(){
        return $this->render('back/mediatheque.html.twig');
    }

    public function dupliquerAction(){
        $idPageOriginale = $this->request->query->get('id');
        $pageOriginale = $this->em->getRepository(Page::class)->find($idPageOriginale);

        $nouvellePage = clone $pageOriginale;
        $ancienSEO = $pageOriginale->getSEO();
        $nouveauSEO = clone $ancienSEO;
        $nouvellePage->setSEO($nouveauSEO);

        $nouvellePage->getSEO()->setUrl($nouvellePage->getSEO()->getUrl().'-copie');

        $menuPage = new menuPage();
        $menuPage->setPage($nouvellePage)->setPosition(0);

        $this->em->persist($nouvellePage);
        $this->em->persist($nouveauSEO);
        $this->em->persist($menuPage);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'edit',
            'entity' => 'Page_Active',
            'id' => $nouvellePage->getId()
        ));
    }

    public function corbeilleAction(){
        $id = $this->request->query->get('id');
        $class = substr($this->request->query->get('entity'), 0, strpos($this->request->query->get('entity'), '_'));

        $entity = $this->em->getRepository('App:'.$class)->find($id);

        if($entity->getCorbeille()){
            $entity->setCorbeille(false);

            if($class == 'Page'){
                $menuPage = new menuPage();
                $menuPage->setPage($entity)->setPosition(0);
                $this->em->persist($menuPage);
                $this->em->flush();
            }

            //Message flash
            $this->addFlash( 'success',
                'L\'élément a été restauré.'
            );
        }else{
            $entity->setCorbeille(true);

            if($class == 'Page'){
                $repoMenuPage = $this->getDoctrine()->getRepository(MenuPage::class);
                $menuPages = $repoMenuPage->findBy(array('page' => $entity));

                foreach($menuPages as $menuPage){
                    $this->em->remove($menuPage);

                    //Si la page était parent d'une autre, on remonte ses enfants à la racine du menu
                    $menuPagesOrphelines = $repoMenuPage->findBy(array('parent' => $menuPage));
                    foreach($menuPagesOrphelines as $menuPage){
                        $menuPage->setParent(null);
                    }
                }

                $this->em->flush();
            }

            //Message flash
            $url = $this->generateUrl('easyadmin',['action'=>'corbeille', 'entity'=>$this->request->query->get('entity'), 'id'=>$entity->getId()]);
            $this->addFlash( 'success',
                sprintf('L\'élément a été mis à la corbeille. <a href="%s">Annuler</a>', $url)
            );
        }
        $this->em->flush();

        if(!$this->request->isXmlHttpRequest()){
            return $this->redirectToRoute('easyadmin', array(
                'action' => 'list',
                'entity' => $this->request->query->get('entity'),
            ));
        }

        return new Response('ok');
    }

    public function voirAction(){
        $entity = $this->request->query->get('entity');
        $id = $this->request->query->get('id');
        if($entity == 'Categorie'){
            $categorie = $this->em->getRepository(Categorie::class)->find($id);
            $urlCategorie = $categorie->getUrl();
            $urlTypeCategorie = $categorie->getTypeCategorie()->getUrl();
            return $this->redirectToRoute('voirCategorie', array('urlTypeCategorie' => $urlTypeCategorie, 'urlCategorie' => $urlCategorie));
        }
        else if($entity == 'TypeCategorie'){
            $typeCategorie = $this->em->getRepository(TypeCategorie::class)->find($id);
            $urlTypeCategorie = $typeCategorie->getUrl();
            return $this->redirectToRoute('voirTypeCategorie', array('urlTypeCategorie' => $urlTypeCategorie));
        }else{
            $page = $this->em->getRepository(Page::class)->find($id);
            $url = $page->getSeo()->getUrl();
            $locale = $page->getLangue()->getAbreviation();
            return $this->redirectToRoute('voirPageLocale', array('_locale' => $locale, 'url' => $url));
        }
    }

    public function activerAction(){
        $idPage = $this->request->query->get('id');
        $page = $this->em->getRepository(Page::class)->find($idPage);

        $activation = $page->getActive();
        if($activation){
            $this->addFlash( 'success',
                'La page a été désactivée.'
            );
        }else{
            $this->addFlash( 'success',
                'La page a été activée.'
            );
        }
        $page->setActive(!$activation);
        $this->em->persist($page);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', array(
                'action' => 'edit',
                'entity' => 'Page_Active',
                'id' => $idPage
            )
        );
    }

    public function voirPagesAction(){
        $idCategorie = $this->request->query->get('id');

        return $this->redirectToRoute('easyadmin', array(
                'action' => 'list',
                'entity' => 'Page_Active',
                'categorie' => $idCategorie
            )
        );
    }
}