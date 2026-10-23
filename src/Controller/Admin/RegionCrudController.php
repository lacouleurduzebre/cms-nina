<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\Region;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RegionCrudController extends BaseCrudController
{
    public const ENTITE = 'Region';

    public static function getEntityFqcn(): string
    {
        return Region::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Région')
            ->setEntityLabelInPlural('Régions')
            ->setDefaultSort(['position' => 'ASC'])
            ->setSearchFields(['nom'])
            ->setPageTitle(Crud::PAGE_EDIT, function (Region $region) {
                return 'Région : '.$region->getNom();
            });
    }

    public function configureFields(string $pageName): iterable
    {
        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            return [
                TextField::new('nom'),
                TextField::new('identifiant'),
                IntegerField::new('position'),
            ];
        }

        return [
            Champ::new('nom'),
            Champ::new('identifiant'),
            Champ::new('position'),
        ];
    }
}
