<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\BlocAnnexe;
use App\Entity\Categorie;
use App\Entity\MenuPage;
use App\Entity\Page;
use App\Entity\SEOPage;
use App\Entity\TypeCategorie;
use App\Service\AdminUrl;
use App\Service\Droits;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\InsufficientEntityPermissionException;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Yaml\Yaml;

/**
 * Comportements communs à tous les CRUD du back-office (repris de l'ancien AdminController d'EasyAdmin 2) :
 * - formulaires nommés comme avec EasyAdmin 2 ("page_active", "groupeblocs"...) pour conserver le thème de formulaire et le JS ;
 * - enregistrement en AJAX (réponses JSON attendues par jsBack.js) ;
 * - palette de blocs, actions corbeille / dupliquer / voir / activer / voirPages.
 */
abstract class BaseCrudController extends AbstractCrudController
{
    //Nom de l'entité dans EasyAdmin 2 (cf. App\Service\AdminUrl::CRUDS)
    public const ENTITE = '';

    //Champs du formulaire rendus avec le thème de formulaire du CMS (blocs, SEO, médiathèque...)
    protected const CHAMPS_THEME_CMS = [];

    //Le formulaire contient des blocs : la palette des blocs disponibles est affichée
    protected const AVEC_BLOCS = false;

    protected $droits;
    protected $adminUrl;

    public function __construct(Droits $droits, AdminUrl $adminUrl)
    {
        $this->droits = $droits;
        $this->adminUrl = $adminUrl;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplates([
                'crud/index' => 'back/crud/index.html.twig',
                'crud/edit' => 'back/crud/edit.html.twig',
                'crud/new' => 'back/crud/new.html.twig',
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $retour = function (Action $action) {
            return $action->setIcon('fas fa-arrow-left')->setLabel('Retour');
        };

        //Les formulaires sont enregistrés par le bouton du CMS (.formulaire-actions-enregistrer, cf. jsBack.js)
        return $actions
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::DELETE)
            ->update(Crud::PAGE_EDIT, Action::DELETE, function (Action $action) {
                return $action->setIcon('fas fa-trash')->setLabel('Supprimer');
            })
            ->update(Crud::PAGE_EDIT, Action::INDEX, $retour)
            ->update(Crud::PAGE_NEW, Action::INDEX, $retour);
    }

    protected function actionVoir(bool $avecLabel = false): Action
    {
        return Action::new('voir', $avecLabel ? 'Voir' : false, 'fas fa-eye')
            ->linkToCrudAction('voir')
            ->setHtmlAttributes(['title' => 'Voir']);
    }

    protected function actionCorbeille(bool $avecLabel = false, string $titre = 'Mettre à la corbeille'): Action
    {
        return Action::new('corbeille', $avecLabel ? $titre : false, 'Restaurer' === $titre ? 'fas fa-recycle' : 'fas fa-trash')
            ->linkToCrudAction('corbeille')
            ->setHtmlAttributes(['title' => $titre]);
    }

    protected function actionDupliquer(bool $avecLabel = false): Action
    {
        return Action::new('dupliquer', $avecLabel ? 'Dupliquer' : false, 'fas fa-copy')
            ->linkToCrudAction('dupliquer')
            ->setHtmlAttributes(['title' => 'Dupliquer']);
    }

    //Contrôle des droits du CMS (cf. service Droits), à surcharger
    protected function controleAcces(string $action, $entite = null): void
    {
    }

    protected function refuserAcces(): void
    {
        throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
    }

    protected function em(): EntityManagerInterface
    {
        return $this->container->get('doctrine')->getManager();
    }

    public function index(AdminContext $context)
    {
        $this->controleAcces(Action::INDEX);

        return parent::index($context);
    }

    public function detail(AdminContext $context)
    {
        $this->controleAcces(Action::DETAIL, $context->getEntity()->getInstance());

        return parent::detail($context);
    }

    public function delete(AdminContext $context)
    {
        $this->controleAcces(Action::DELETE, $context->getEntity()->getInstance());

        return parent::delete($context);
    }

    /* Formulaires */

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        return $this->createFormBuilderCms($entityDto, $formOptions, $context, 'edit');
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        return $this->createFormBuilderCms($entityDto, $formOptions, $context, 'new');
    }

    //Équivalent de FormFactory::create(Edit|New)FormBuilder d'EasyAdmin, avec le nom de formulaire d'EasyAdmin 2
    private function createFormBuilderCms(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context, string $vue): FormBuilderInterface
    {
        $nom = strtolower(static::ENTITE);

        $formOptions->set('attr.class', trim(($formOptions->get('attr.class') ?? '').' ea-'.$context->getCrud()->getCurrentAction().'-form '.$vue.'-form'));
        $formOptions->set('attr.id', $vue.'-'.$nom.'-form');
        $formOptions->set('attr.data-view', $vue);
        $formOptions->set('attr.data-entity', static::ENTITE);
        $formOptions->set('attr.data-entity-id', $entityDto->getPrimaryKeyValue());
        $formOptions->set('attr.novalidate', 'novalidate');
        $formOptions->set('attr.autocomplete', 'off');
        $formOptions->set('entityDto', $entityDto);
        $formOptions->setIfNotSet('translation_domain', $context->getI18n()->getTranslationDomain());

        return $this->container->get('form.factory')->createNamedBuilder($nom, CrudFormType::class, $entityDto->getInstance(), $formOptions->all());
    }

    /* Enregistrement en AJAX : jsBack.js attend une réponse JSON */

    public function edit(AdminContext $context)
    {
        $this->controleAcces(Action::EDIT, $context->getEntity()->getInstance());

        if (!$this->estEnregistrementAjax($context)) {
            return parent::edit($context);
        }

        $this->verifierAction($context, Action::EDIT, $context->getEntity());
        $form = $this->formulaireSoumis($context, Crud::PAGE_EDIT);
        if (!$form->isValid()) {
            return new JsonResponse(['erreurs' => true, 'tpl' => false]);
        }

        $entite = $context->getEntity()->getInstance();
        $event = new BeforeEntityUpdatedEvent($entite);
        $this->container->get('event_dispatcher')->dispatch($event);
        $entite = $event->getEntityInstance();

        $this->updateEntity($this->em(), $entite);

        $this->container->get('event_dispatcher')->dispatch(new AfterEntityUpdatedEvent($entite));

        $tpl = $this->renderView('back/messageEnregistrement.html.twig', ['entite' => $entite]);

        return new JsonResponse(['erreurs' => false, 'tpl' => $tpl]);
    }

    public function new(AdminContext $context)
    {
        $this->controleAcces(Action::NEW);

        if (!$this->estEnregistrementAjax($context)) {
            return parent::new($context);
        }

        $this->verifierAction($context, Action::NEW, null);
        $context->getEntity()->setInstance($this->createEntity($context->getEntity()->getFqcn()));
        $form = $this->formulaireSoumis($context, Crud::PAGE_NEW);
        if (!$form->isValid()) {
            return new JsonResponse(['erreurs' => true]);
        }

        $entite = $form->getData();
        $event = new BeforeEntityPersistedEvent($entite);
        $this->container->get('event_dispatcher')->dispatch($event);
        $entite = $event->getEntityInstance();

        $this->persistEntity($this->em(), $entite);

        $this->container->get('event_dispatcher')->dispatch(new AfterEntityPersistedEvent($entite));

        $this->addFlash('enregistrement', $entite.' : enregistrement terminé');

        return new JsonResponse(['erreurs' => false, 'redirection' => $this->adminUrl->crud(static::ENTITE, Action::EDIT, $entite->getId())]);
    }

    //Enregistrement sans AJAX (JS désactivé) : le bouton d'enregistrement du CMS n'est pas un bouton d'EasyAdmin
    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        if (isset($context->getRequest()->request->all()['ea']['newForm']['btn'])) {
            return parent::getRedirectResponseAfterSave($context, $action);
        }

        return $this->redirect($this->adminUrl->crud(static::ENTITE, Action::EDIT, $context->getEntity()->getPrimaryKeyValue()));
    }

    private function estEnregistrementAjax(AdminContext $context): bool
    {
        $request = $context->getRequest();

        return $request->isXmlHttpRequest() && $request->isMethod('POST');
    }

    private function verifierAction(AdminContext $context, string $action, ?EntityDto $entite): void
    {
        if (!$this->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => $action, 'entity' => $entite])) {
            throw new ForbiddenActionException($context);
        }

        if (!$context->getEntity()->isAccessible()) {
            throw new InsufficientEntityPermissionException($context);
        }
    }

    private function formulaireSoumis(AdminContext $context, string $page): FormInterface
    {
        $this->container->get(EntityFactory::class)->processFields($context->getEntity(), FieldCollection::new($this->configureFields($page)));

        $form = Crud::PAGE_EDIT === $page
            ? $this->createEditForm($context->getEntity(), $context->getCrud()->getEditFormOptions(), $context)
            : $this->createNewForm($context->getEntity(), $context->getCrud()->getNewFormOptions(), $context);
        $form->handleRequest($context->getRequest());

        return $form;
    }

    /* Paramètres des templates */

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $responseParameters->set('entite', static::ENTITE);
        $responseParameters->set('champsThemeCms', static::CHAMPS_THEME_CMS);

        $pageName = $responseParameters->get('pageName');
        if (static::AVEC_BLOCS && in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW], true)) {
            $instance = Crud::PAGE_EDIT === $pageName ? $responseParameters->get('entity')->getInstance() : null;
            $responseParameters->set('blocs', $this->listeBlocs('contenu'));
            $responseParameters->set('blocsAnnexes', $this->listeBlocs('annexe', $instance));
        }

        return $responseParameters;
    }

    protected function listeBlocs($typeBloc, $entity = null){
        $dossier = $this->getParameter('kernel.project_dir').'/src/Blocs';
        $types = scandir($dossier);
        $types = array_combine(array_values($types), array_values($types));
        unset($types["."]);
        unset($types[".."]);
        unset($types["configBlocs.yaml"]);
        $config = Yaml::parseFile($dossier.'/configBlocs.yaml');

        $repoBlocAnnexe = $this->em()->getRepository(BlocAnnexe::class);

        $blocs = [];
        foreach($types as $type){
            if(file_exists($dossier.'/'.$type.'/infos.yaml')){
                $infos = Yaml::parseFile($dossier.'/'.$type.'/infos.yaml');
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

    //Équivalent du type "collection" d'EasyAdmin 2
    protected function collection(string $propriete, string $typeEntree): Champ
    {
        return Champ::new($propriete)->setFormType(CollectionType::class)->setFormTypeOptions([
            'entry_type' => $typeEntree,
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ]);
    }

    //Onglets / groupes de champs, avec les icônes Font Awesome d'EasyAdmin 2
    protected function onglet(string $label, ?string $icone = null, string $cssClass = ''): FormField
    {
        $onglet = FormField::addTab($label, $icone ? 'fa fa-'.$icone : null);

        return $cssClass ? $onglet->setCssClass($cssClass) : $onglet;
    }

    protected function groupe(string $label, ?string $icone = null, ?string $aide = null, string $cssClass = ''): FormField
    {
        $groupe = FormField::addPanel($label, $icone ? 'fas fa-'.$icone : null);
        if ($aide) {
            $groupe->setHelp($aide);
        }

        return $cssClass ? $groupe->setCssClass($cssClass) : $groupe;
    }

    /* Actions personnalisées */

    public function corbeille(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();
        $em = $this->em();

        if($entity->getCorbeille()){
            $entity->setCorbeille(false);

            if($entity instanceof Page){
                $menuPage = new MenuPage();
                $menuPage->setPage($entity)->setPosition(0);
                $em->persist($menuPage);
                $em->flush();
            }

            //Message flash
            $this->addFlash( 'success',
                'L\'élément a été restauré.'
            );
        }else{
            $entity->setCorbeille(true);

            if($entity instanceof Page){
                $repoMenuPage = $em->getRepository(MenuPage::class);
                $menuPages = $repoMenuPage->findBy(array('page' => $entity));

                foreach($menuPages as $menuPage){
                    $em->remove($menuPage);

                    //Si la page était parent d'une autre, on remonte ses enfants à la racine du menu
                    $menuPagesOrphelines = $repoMenuPage->findBy(array('parent' => $menuPage));
                    foreach($menuPagesOrphelines as $menuPage){
                        $menuPage->setParent(null);
                    }

                    //Si la page était la page d'accueil, la langue de la page n'a plus de page d'accueil
                    $langue = $menuPage->getPage()->getLangue();
                    if($langue->getPageAccueil() == $menuPage->getPage()){
                        $langue->setPageAccueil(null);
                    }
                }

                $em->flush();
            }

            //Message flash
            $url = $this->adminUrl->crud(static::ENTITE, 'corbeille', $entity->getId());
            $this->addFlash( 'success',
                sprintf('L\'élément a été mis à la corbeille. <a href="%s">Annuler</a>', $url)
            );
        }
        $em->flush();

        if(!$context->getRequest()->isXmlHttpRequest()){
            return $this->redirect($this->adminUrl->crud(static::ENTITE));
        }

        return new Response('ok');
    }

    public function dupliquer(AdminContext $context)
    {
        $em = $this->em();
        $pageOriginale = $context->getEntity()->getInstance();

        $nouvellePage = clone $pageOriginale;

        //SEO
        $ancienSEO = $pageOriginale->getSEO();
        $nouveauSEO = clone $ancienSEO;
        $nouvellePage->setSEO($nouveauSEO);
            //Vérif url
        $repoSEO = $em->getRepository(SEOPage::class);
        $url = $nouvellePage->getSEO()->getUrl().'-copie';
        while($repoSEO->findOneBy(array('url' => $url))){
            $url = $url.'-copie';
        }
        if(strlen($url) > 190){//Nouvelle url trop longue -> erreur, on arrête la duplication
            $this->addFlash('-danger', "L'url du nouveau contenu serait trop longue, réduisez la longueur de l'url du contenu que vous souhaitez dupliquer");
            return $this->redirect($context->getReferrer() ?? $this->adminUrl->crud(static::ENTITE, Action::EDIT, $pageOriginale->getId()));
        }
        $nouvellePage->getSEO()->setUrl($url);
        $em->persist($nouveauSEO);

        //Menu page
        $menuPage = new MenuPage();
        $menuPage->setPage($nouvellePage)->setPosition(0);
        $em->persist($menuPage);

        //Blocs
        $blocs = $pageOriginale->getBlocs();
        foreach($blocs as $ancienBloc){
            $nouveauBloc = clone $ancienBloc;
            $nouvellePage->addBloc($nouveauBloc);
            $em->persist($nouveauBloc);

            if($nouveauBloc->getType() == 'Section'){
                $this->duplicationBlocsEnfants($nouveauBloc);
            }
        }

        $em->persist($nouvellePage);
        $em->flush();

        return $this->redirect($this->adminUrl->crud('Page_Active', Action::EDIT, $nouvellePage->getId()));
    }

    private function duplicationBlocsEnfants($bloc){
        $blocsEnfants = $bloc->getBlocsEnfants();
        foreach($blocsEnfants as $blocEnfant){
            $nouveauBlocEnfant = clone $blocEnfant;
            $nouveauBlocEnfant->setBlocParent($bloc);
            $this->em()->persist($nouveauBlocEnfant);

            if($nouveauBlocEnfant->getType() == 'Section'){
                $this->duplicationBlocsEnfants($nouveauBlocEnfant);
            }
        }
    }

    public function voir(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();

        if($entity instanceof Categorie){
            $urlCategorie = $entity->getSEO()->getUrl();
            $urlTypeCategorie = $entity->getTypeCategorie()->getSEO()->getUrl();
            return $this->redirectToRoute('voirCategorie', array('urlTypeCategorie' => $urlTypeCategorie, 'urlCategorie' => $urlCategorie));
        }
        else if($entity instanceof TypeCategorie){
            $urlTypeCategorie = $entity->getSEO()->getUrl();
            return $this->redirectToRoute('voirTypeCategorie', array('urlTypeCategorie' => $urlTypeCategorie));
        }else{
            $url = $entity->getSeo()->getUrl();
            $locale = $entity->getLangue()->getAbreviation();
            return $this->redirectToRoute('voirPageLocale', array('_locale' => $locale, 'url' => $url));
        }
    }

    public function activer(AdminContext $context)
    {
        $page = $context->getEntity()->getInstance();

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
        $this->em()->persist($page);
        $this->em()->flush();

        return $this->redirect($this->adminUrl->crud('Page_Active', Action::EDIT, $page->getId()));
    }

    public function voirPages(AdminContext $context)
    {
        return $this->redirect($this->adminUrl->crud('Page_Active', Action::INDEX, null, ['categorie' => $context->getEntity()->getPrimaryKeyValue()]));
    }
}
