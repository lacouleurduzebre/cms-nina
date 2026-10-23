<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\Categorie;
use App\Form\Type\SEOCategorieType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategorieCrudController extends BaseCrudController
{
    public const ENTITE = 'Categorie';
    protected const CHAMPS_THEME_CMS = ['SEO'];

    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Catégorie')
            ->setEntityLabelInPlural('Catégories')
            ->setDefaultSort(['nom' => 'ASC'])
            ->setSearchFields(['nom', 'typeCategorie.nom'])
            ->setPageTitle(Crud::PAGE_EDIT, function (Categorie $categorie) {
                return 'Catégorie : '.$categorie->getNom();
            });
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $this->actionVoir())
            ->add(Crud::PAGE_INDEX, Action::new('voirPages', false, 'fas fa-file-alt')
                ->linkToCrudAction('voirPages')
                ->setHtmlAttributes(['title' => 'Voir les pages de cette catégorie']))
            ->add(Crud::PAGE_EDIT, $this->actionVoir(true));
    }

    public function configureFields(string $pageName): iterable
    {
        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            return [
                TextField::new('nom'),
                TextField::new('typeCategorie.nom', 'Type'),
                TextField::new('categorieParent', 'Catégorie parent'),
                TextField::new('seo.url', 'Url'),
                TextField::new('description')->setMaxLength(100),
            ];
        }

        return [
            $this->onglet('Catégorie'),
            Champ::new('nom'),
            Champ::new('langue'),
            Champ::new('description'),
            Champ::new('categorieParent', 'Catégorie parent')->setFormType(EntityType::class)->setFormTypeOption('class', Categorie::class),
            Champ::new('typeCategorie', 'Type'),

            $this->onglet('Référencement'),
            Champ::new('SEO')->setFormType(SEOCategorieType::class)->setFormTypeOptions(['by_reference' => true, 'label' => false]),
        ];
    }
}
