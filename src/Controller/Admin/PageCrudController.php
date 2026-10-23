<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Controller\Admin\Action\ActionActiver;
use App\Controller\Admin\Action\ActionCorbeille;
use App\Controller\Admin\Action\ActionDupliquer;
use App\Controller\Admin\Action\ActionVoir;
use App\Entity\Categorie;
use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Page;
use App\Form\Type\BlocAnnexeType;
use App\Form\Type\BlocType;
use App\Form\Type\CommentaireType;
use App\Form\Type\SEOPageType;
use App\Form\Type\TraductionsType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PageCrudController extends BaseCrudController
{
    use ActionActiver;
    use ActionCorbeille;
    use ActionDupliquer;
    use ActionVoir;

    public const ENTITE = 'Page_Active';
    protected const CHAMPS_THEME_CMS = ['blocs', 'blocsAnnexes', 'SEO', 'auteur', 'commentaires'];
    protected const AVEC_BLOCS = true;

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Page')
            ->setEntityLabelInPlural('Pages')
            ->setDefaultSort(['dateCreation' => 'DESC'])
            ->setSearchFields(['titre', 'categories.nom', 'langue.nom'])
            ->setPageTitle(Crud::PAGE_EDIT, function (Page $page) {
                return 'Page : '.$page->getTitre();
            });
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $this->actionVoir())
            ->add(Crud::PAGE_INDEX, $this->actionDupliquer())
            ->add(Crud::PAGE_INDEX, $this->actionCorbeille())
            ->add(Crud::PAGE_EDIT, $this->actionVoir(true))
            ->add(Crud::PAGE_EDIT, $this->actionDupliquer(true))
            ->add(Crud::PAGE_EDIT, $this->actionCorbeille(true))
            ->update(Crud::PAGE_EDIT, Action::INDEX, function (Action $action) {
                return $action->setLabel('Retour à la liste des pages');
            })
            ->remove(Crud::PAGE_EDIT, Action::DELETE)
            ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                TextField::new('titre'),
                AssociationField::new('auteur'),
                AssociationField::new('categories', 'Catégories'),
                DateTimeField::new('dateCreation', 'Date de création'),
                DateTimeField::new('datePublication', 'Date de publication'),
                AssociationField::new('langue'),
                BooleanField::new('active'),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                TextField::new('titre'),
                AssociationField::new('auteur'),
                AssociationField::new('auteurDerniereModification'),
                AssociationField::new('categories'),
                DateTimeField::new('dateCreation'),
                DateTimeField::new('datePublication', 'Date de publication'),
                DateTimeField::new('dateDepublication', 'Date de dépublication'),
                AssociationField::new('langue'),
                BooleanField::new('active'),
            ];
        }

        return [
            $this->onglet('Contenus'),
            Champ::new('titre')->setFormType(TextType::class)->setHelp('Utilisé en tant que balise H1'),
            Champ::new('titreMenu', 'Titre dans les menus'),
            $this->collection('blocs', BlocType::class),

            $this->onglet('Référencement'),
            Champ::new('SEO')->setFormType(SEOPageType::class)->setFormTypeOptions(['by_reference' => true, 'label' => false]),

            $this->onglet('Affichage'),
            $this->groupe('Affichage des pages enfants', 'file-alt'),
            Champ::new('affichageSousNiveaux', 'Afficher les pages enfants dans les menus'),
            $this->groupe('Publication', 'newspaper'),
            Champ::new('affichageDatePublication', 'Afficher la date de publication'),
            Champ::new('datePublication', 'Date de publication')->setFormTypeOptions(['date_widget' => 'single_text', 'time_widget' => 'single_text']),
            Champ::new('dateDepublication', 'Date de dépublication')->setFormTypeOptions(['date_widget' => 'single_text', 'time_widget' => 'single_text']),
            $this->groupe('Auteur(s)', 'feather-alt'),
            Champ::new('affichageAuteur', "Afficher l'auteur"),
            Champ::new('auteur')->setFormTypeOption('disabled', true),
            Champ::new('auteurDerniereModification'),
            $this->groupe('Blocs annexes', 'shapes'),
            $this->collection('blocsAnnexes', BlocAnnexeType::class)->setLabel(false),

            $this->onglet('Catégories'),
            Champ::new('categories')->setFormTypeOptions([
                'query_builder' => [CategorieRepository::class, 'orderByNom'],
                'group_by' => 'typeCategorie',
            ]),

            $this->onglet('Commentaires'),
            Champ::new('affichageCommentaires', 'Autoriser les commentaires'),
            $this->collection('commentaires', CommentaireType::class),

            //Onglet masqué s'il n'y a qu'une langue active (cf. back/crud/_formulaire.html.twig), mais toujours soumis
            $this->onglet('Traductions', null, 'traductions'),
            Champ::new('langue', 'Page en :')->setFormTypeOption('required', true),
            Champ::new('traductions', 'Traductions :')->setFormType(TraductionsType::class),
        ];
    }

    //Pages hors corbeille ; ?categorie=X : pages d'une catégorie
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.corbeille = false');

        $categorie = $this->getContext()->getRequest()->query->get('categorie');
        if ($categorie) {
            $qb->andWhere(':categorie MEMBER OF entity.categories')
                ->setParameter('categorie', $categorie);
        }

        return $qb;
    }

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $responseParameters = parent::configureResponseParameters($responseParameters);

        $idCategorie = $this->getContext()->getRequest()->query->get('categorie');
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName') && $idCategorie) {
            $responseParameters->set('filtreCategorie', $this->em()->getRepository(Categorie::class)->find($idCategorie));
        }

        return $responseParameters;
    }

    //Nouvelle page : options d'affichage par défaut de la configuration ; traduction : reprise de la page originale
    public function createEntity(string $entityFqcn): object
    {
        $request = $this->getContext()->getRequest();
        $em = $this->em();

        if($request->query->get('pageOriginale')){
            $pageOriginale = $em->getRepository(Page::class)->find($request->query->get('pageOriginale'));

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

            $langue = $em->getRepository(Langue::class)->find($request->query->get('langue'));
            $nvPage->setLangue($langue);

            $trads = $nvPage->getTraductions();
            $trads[$pageOriginale->getLangue()->getId()] = $request->query->get('pageOriginale');
            $nvPage->setTraductions($trads);
        }else{
            $nvPage = new Page();
        }

        $config = $em->getRepository(Configuration::class)->find(1);
        $nvPage->setAffichageCommentaires($config->getAffichageCommentaires());
        $nvPage->setAffichageDatePublication($config->getAffichageDatePublication());
        $nvPage->setAffichageAuteur($config->getAffichageAuteur());

        return $nvPage;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        $this->setTraductions($entityInstance);

        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->setTraductions($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    protected function setTraductions($entity){
        $em = $this->em();
        $repoPage = $em->getRepository(Page::class);

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
}
