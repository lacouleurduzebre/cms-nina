<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Controller\Admin\Action\ActionVoir;
use App\Entity\TypeCategorie;
use App\Form\Type\SEOTypeCategorieType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TypeCategorieCrudController extends BaseCrudController
{
    use ActionVoir;

    public const ENTITE = 'TypeCategorie';
    protected const CHAMPS_THEME_CMS = ['SEO'];

    public static function getEntityFqcn(): string
    {
        return TypeCategorie::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Type de catégories')
            ->setEntityLabelInPlural('Types de catégories')
            ->setSearchFields(['nom', 'description'])
            ->setPageTitle(Crud::PAGE_EDIT, function (TypeCategorie $typeCategorie) {
                return 'Type de catégories : '.$typeCategorie->getNom();
            });
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $this->actionVoir())
            ->add(Crud::PAGE_EDIT, $this->actionVoir(true));
    }

    public function configureFields(string $pageName): iterable
    {
        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            return [
                TextField::new('nom'),
                TextField::new('description')->renderAsHtml(),
                TextField::new('seo.url', 'Url'),
            ];
        }

        return [
            $this->onglet('Type de catégorie'),
            Champ::new('nom'),
            Champ::new('langue'),
            Champ::new('description'),

            $this->onglet('Référencement'),
            Champ::new('SEO')->setFormType(SEOTypeCategorieType::class)->setFormTypeOptions(['by_reference' => true, 'label' => false]),
        ];
    }
}
