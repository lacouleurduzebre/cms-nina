<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\GroupeBlocs;
use App\Form\Type\BlocType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GroupeBlocsCrudController extends BaseCrudController
{
    public const ENTITE = 'GroupeBlocs';
    protected const CHAMPS_THEME_CMS = ['blocs'];
    protected const AVEC_BLOCS = true;

    public static function getEntityFqcn(): string
    {
        return GroupeBlocs::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Groupe de blocs')
            ->setEntityLabelInPlural('Groupes de blocs')
            ->setSearchFields(['nom', 'region.nom'])
            ->setPageTitle(Crud::PAGE_EDIT, function (GroupeBlocs $groupeBlocs) {
                return 'Groupe de blocs : '.$groupeBlocs->getNom();
            });
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                TextField::new('nom'),
                TextField::new('identifiant'),
                AssociationField::new('region', 'Région'),
                AssociationField::new('langue'),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                TextField::new('nom'),
                AssociationField::new('region', 'Région'),
                AssociationField::new('blocs'),
            ];
        }

        return [
            $this->onglet('Contenus'),
            Champ::new('nom'),
            $this->collection('blocs', BlocType::class),

            $this->onglet('Paramètres'),
            Champ::new('langue'),
            Champ::new('identifiant'),
            Champ::new('region'),
            Champ::new('position'),
        ];
    }
}
