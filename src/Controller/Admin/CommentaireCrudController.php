<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Controller\Admin\Action\ActionCorbeille;
use App\Entity\Commentaire;
use App\Entity\Page;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentaireCrudController extends BaseCrudController
{
    use ActionCorbeille;

    public const ENTITE = 'Commentaire_Actif';
    protected const CORBEILLE = false;

    public static function getEntityFqcn(): string
    {
        return Commentaire::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Commentaire')
            ->setEntityLabelInPlural('Commentaires')
            ->setDefaultSort(['page.titre' => 'ASC'])
            ->setSearchFields(['page.titre', 'auteur']);
    }

    public function configureActions(Actions $actions): Actions
    {
        if (static::CORBEILLE) {
            return parent::configureActions($actions)
                ->add(Crud::PAGE_INDEX, $this->actionCorbeille(false, 'Restaurer'))
                ->disable(Action::EDIT);
        }

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $this->actionCorbeille())
            ->add(Crud::PAGE_EDIT, $this->actionCorbeille(true))
            ->remove(Crud::PAGE_EDIT, Action::DELETE)
            ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            return [
                TextField::new('page.titre', 'Page'),
                TextField::new('auteur', 'Auteur'),
                DateTimeField::new('date'),
                TextField::new('contenu')->setMaxLength(100),
                BooleanField::new('valide', 'Validé'),
            ];
        }

        $champs = [
            Champ::new('page', 'Page')->setFormType(EntityType::class)->setFormTypeOption('class', Page::class),
            Champ::new('auteur', 'Auteur')->setFormType(TextType::class),
            Champ::new('email'),
            Champ::new('site'),
            Champ::new('date'),
            Champ::new('contenu')->setFormType(TextType::class),
        ];

        if (Crud::PAGE_EDIT === $pageName) {
            $champs[] = Champ::new('valide', 'Validé');
            $champs[] = Champ::new('corbeille', 'Activé');
        }

        return $champs;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.corbeille = '.(static::CORBEILLE ? 'true' : 'false'));
    }
}
