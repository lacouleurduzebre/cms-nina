<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Action\ActionCorbeille;
use App\Entity\Page;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PageCorbeilleCrudController extends BaseCrudController
{
    use ActionCorbeille;

    public const ENTITE = 'Page_Corbeille';

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Page à la corbeille')
            ->setEntityLabelInPlural('Pages à la corbeille')
            ->setDefaultSort(['titre' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $this->actionCorbeille(false, 'Restaurer'))
            ->disable(Action::EDIT, Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre'),
            AssociationField::new('auteur'),
            DateTimeField::new('dateCreation', 'Date de création'),
            DateTimeField::new('datePublication', 'Date de publication'),
            AssociationField::new('langue'),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.corbeille = true');
    }
}
