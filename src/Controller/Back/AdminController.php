<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 19/02/2018
 * Time: 10:28
 */

namespace App\Controller\Back;

use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\SEO;
use App\Entity\TypeCategorie;
use App\Entity\Utilisateur;
use App\Twig\Front\BlocAnnexe;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
    protected function updateEntity($entity)//edit
    {
        parent::updateEntity($entity);

        $this->addFlash('enregistrement', "<span>\"".$entity."\" : enregistrement terminé</span>");
    }

    protected function persistEntity($entity)//new
    {
        parent::persistEntity($entity);

        $this->addFlash('enregistrement', "<span>\"".$entity."\" : enregistrement terminé</span>");
    }

    protected function persistPage_ActiveEntity($entity)//new
    {
        $this::persistEntity($entity);

        $this->setTraductions($entity);

        $this->getDoctrine()->getManager()->flush();
    }

    protected function updatePage_ActiveEntity($entity)//edit
    {
        $this->setTraductions($entity);

        $this::updateEntity($entity);
    }

    protected function setTraductions($entity){
        $em = $this->getDoctrine()->getManager();
        $repoPage = $this->getDoctrine()->getRepository(Page::class);

        $langue = $entity->getLangue()->getId();
        $id = $entity->getId();
        $traductions = $entity->getTraductions();

        foreach($traductions as $traduction){
            if($traduction){
                $page = $repoPage->find($traduction);
                $trads = $page->getTraductions();
                $trads[$langue] = $id;
                $page->setTraductions($trads);
                $em->persist($page);
            }
        }
    }

    //Édition d'une langue : seule les pages dans cette langue peuvent être choisies comme page d'accueil
    protected function createLangueEntityFormBuilder($entity, $view){
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        if($view == "edit" && $formBuilder->getData()) {

            $langue = $formBuilder->getData();

            $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($langue) {
                $event->getForm()->add('pageAccueil', EntityType::class, [
                    'required' => false,
                    'class' => Page::class,
                    'choice_label' => 'titreMenu',
                    'query_builder' => function (EntityRepository $er) use ($langue) {
                        return $er->createQueryBuilder('p')
                            ->andWhere('p.langue = :langue')
                            ->setParameters(array('langue' => $langue))
                            ->orderBy('p.titreMenu', 'ASC');
                    }
                ]);

            });
        }
        return $formBuilder;
    }

    //Nouvelle page : checkboxes affichage commentaires / date publi / auteur en fonction de la config
    protected function createPage_ActiveEntityFormBuilder($entity, $view){
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        if($view == "new") {
            $config = $this->getDoctrine()->getRepository(Configuration::class)->find(1);
            $page = $formBuilder->getData();

            $page->setAffichageCommentaires($config->getAffichageCommentaires());
            $page->setAffichageDatePublication($config->getAffichageDatePublication());
            $page->setAffichageAuteur($config->getAffichageAuteur());
        }

        return $formBuilder;
    }

    //Ajout de la liste des blocs dans $parameters
    protected function newPage_ActiveAction(){
        return $this->newAvecListeBlocs();
    }

    protected function newGroupeBlocsAction(){
        return $this->newAvecListeBlocs();
    }

    //Traductions de page : ajout de la langue et de la page originale dans les données du formulaire
    protected function createNewPage_ActiveEntity()
    {
        if($this->request->get('pageOriginale')){
            $pageOriginale = $this->getDoctrine()->getRepository(Page::class)->find($this->request->get('pageOriginale'));

            $nvPage = new Page();

            //Contenus
            $nvPage->setTitre($pageOriginale->getTitre());
            $nvPage->setTitreMenu($pageOriginale->getTitreMenu());

            $blocs = $pageOriginale->getBLocs();
            foreach($blocs as $bloc){
                $nvBloc = clone $bloc;
                $nvPage->addBloc($nvBloc);
            }

            //SEO
            $nvSEO = clone $pageOriginale->getSEO();
            $nvPage->setSEO($nvSEO);

            //Affichage
            $blocsAnnexes = $pageOriginale->getBLocsAnnexes();
            foreach($blocsAnnexes as $blocAnnexe){
                $nvBlocAnnexe = clone $blocAnnexe;
                $nvPage->addBlocsAnnex($nvBlocAnnexe);
            }

            //Catégories
            $categories = $pageOriginale->getCategories();
            foreach($categories as $categorie){
                $nvPage->addCategory($categorie);
            }

           $langue = $this->getDoctrine()->getRepository(Langue::class)->find($this->request->get('langue'));
            $nvPage->setLangue($langue);

            $trads = $nvPage->getTraductions();
            $trads[$pageOriginale->getLangue()->getId()] = $this->request->get('pageOriginale');
            $nvPage->setTraductions($trads);

            return $nvPage;
        }else{
            return parent::createNewEntity();
        }
    }

    private function newAvecListeBlocs(){
        $this->dispatch(EasyAdminEvents::PRE_NEW);

        $entity = $this->executeDynamicMethod('createNew<EntityName>Entity');

        $blocs = $this->listeBlocs('contenu');
        $blocsAnnexes = $this->listeBlocs('annexe');

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', array($entity, $fields));

        $newForm->handleRequest($this->request);
        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_PERSIST, array('entity' => $entity));

            $this->executeDynamicMethod('prePersist<EntityName>Entity', array($entity));
            $this->executeDynamicMethod('persist<EntityName>Entity', array($entity));

            $this->dispatch(EasyAdminEvents::POST_PERSIST, array('entity' => $entity));

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, array(
            'entity_fields' => $fields,
            'form' => $newForm,
            'entity' => $entity,
        ));

        $parameters = array(
            'form' => $newForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'blocs' => $blocs,
            'blocsAnnexes' => $blocsAnnexes
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('new', $this->entity['templates']['new'], $parameters));
    }

    //Ajout de la liste des blocs dans $parameters
    protected function editPage_ActiveAction(){
        return $this->editAvecListeBlocs();
    }

    protected function editGroupeBlocsAction(){
        return $this->editAvecListeBlocs();
    }

    private function editAvecListeBlocs(){
        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            // cast to integer instead of string to avoid sending empty responses for 'false'
            return new Response((int) $newValue);
        }

        $blocs = $this->listeBlocs('contenu');
        $blocsAnnexes = $this->listeBlocs('annexe', $entity);

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->executeDynamicMethod('create<EntityName>EditForm', array($entity, $fields));
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, array('entity' => $entity));

            $this->executeDynamicMethod('preUpdate<EntityName>Entity', array($entity));
            $this->executeDynamicMethod('update<EntityName>Entity', array($entity));

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        $parameters = array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'blocs' => $blocs,
            'blocsAnnexes' => $blocsAnnexes
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('edit', $this->entity['templates']['edit'], $parameters));
    }

    protected function listeBlocs($typeBloc, $entity = null){
        $types = scandir('../src/Blocs');
        $types = array_combine(array_values($types), array_values($types));
        unset($types["."]);
        unset($types[".."]);
        unset($types["configBlocs.yaml"]);
        $config = Yaml::parseFile('../src/Blocs/configBlocs.yaml');

        $repoBlocAnnexe = $this->getDoctrine()->getRepository(\App\Entity\BlocAnnexe::class);

        $blocs = [];
        foreach($types as $type){
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
     * @Route("", name="tableauDeBord")
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

        return $this->render('back/tableauDeBord.html.twig', array(
            'user' => $user,
            'blocs' => $blocs,
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
            $url = $this->generateUrl('admin',['action'=>'corbeille', 'entity'=>$this->request->query->get('entity'), 'id'=>$entity->getId()]);
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
            return $this->redirectToRoute('voirPage', array('_locale' => $locale, 'url' => $url));
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

        return $this->redirectToRoute('admin', array(
                'action' => 'edit',
                'entity' => 'Page_Active',
                'id' => $idPage
            )
        );
    }
}