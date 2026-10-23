<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MenuCrudController extends BaseCrudController
{
    public const ENTITE = 'Menu';

    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Menu')
            ->setEntityLabelInPlural('Menus')
            ->setDefaultSort(['priorite' => 'ASC'])
            ->setSearchFields(['nom'])
            ->setPageTitle(Crud::PAGE_EDIT, function (Menu $menu) {
                return 'Menu : '.$menu->getNom();
            });
    }

    public function configureFields(string $pageName): iterable
    {
        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            return [
                TextField::new('nom'),
                AssociationField::new('langue'),
                BooleanField::new('defaut', 'Menu principal ?'),
                IntegerField::new('priorite'),
            ];
        }

        //La langue d'un menu n'est plus modifiable après sa création
        return array_values(array_filter([
            Champ::new('nom'),
            Crud::PAGE_NEW === $pageName ? Champ::new('langue') : null,
            Champ::new('priorite'),
        ]));
    }
}
