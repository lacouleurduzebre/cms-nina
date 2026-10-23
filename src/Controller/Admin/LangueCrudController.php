<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\Langue;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LangueCrudController extends BaseCrudController
{
    public const ENTITE = 'Langue';

    public static function getEntityFqcn(): string
    {
        return Langue::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Langue')
            ->setEntityLabelInPlural('Langues')
            ->setDefaultSort(['abreviation' => 'ASC'])
            ->setSearchFields(['nom'])
            ->setPageTitle(Crud::PAGE_EDIT, function (Langue $langue) {
                return 'Langue : '.$langue->getNom();
            });
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                TextField::new('nom'),
                TextField::new('abreviation'),
                BooleanField::new('active'),
                BooleanField::new('defaut'),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                TextField::new('abreviation'),
                TextField::new('nom'),
                BooleanField::new('active'),
            ];
        }

        $champs = [
            Champ::new('nom'),
            Champ::new('abreviation', 'Abréviation')->setHelp('deux lettres, de préférence en minuscules'),
            Champ::new('code', 'Code de la langue sous la forme xx-XX'),
            Champ::new('metaTitre', 'Meta-titre général pour cette langue'),
            Champ::new('metaDescription', 'Meta-description générale pour cette langue')->setFormTypeOption('attr', ['class' => 'notTinymce']),
        ];

        //Édition : seules les pages dans cette langue peuvent être choisies comme page d'accueil
        if (Crud::PAGE_EDIT === $pageName) {
            $langue = $this->getContext()->getEntity()->getInstance();
            $champs[] = Champ::new('pageAccueil')->setFormType(EntityType::class)->setFormTypeOptions([
                'required' => false,
                'class' => Page::class,
                'choice_label' => 'titreMenu',
                'query_builder' => function (EntityRepository $er) use ($langue) {
                    return $er->createQueryBuilder('p')
                        ->andWhere('p.langue = :langue')
                        ->andWhere('p.corbeille = 0')
                        ->setParameters(array('langue' => $langue))
                        ->orderBy('p.titreMenu', 'ASC');
                },
            ]);
        }

        return $champs;
    }

    //Suppression d'une langue : réinitialisation cookie langueArbo
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);

        if (isset($_COOKIE['langueArbo'])) {
            unset($_COOKIE['langueArbo']);
            setcookie('langueArbo', '', time() - 3600, '/');
        }
    }
}
