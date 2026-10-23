<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\Utilisateur;
use App\Form\Type\RoleType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UtilisateurCrudController extends BaseCrudController
{
    public const ENTITE = 'Utilisateur';
    protected const CHAMPS_THEME_CMS = ['imageProfil'];

    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    //Gestion des comptes réservée au droit "comptes" ; chacun peut éditer son propre profil
    protected function controleAcces(string $action, $entite = null): void
    {
        if ($this->droits->checkDroit('comptes')) {
            return;
        }

        if (Action::EDIT === $action && $entite instanceof Utilisateur && $entite->getId() === $this->getUser()->getId()) {
            return;
        }

        $this->refuserAcces();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setSearchFields(['username', 'email'])
            ->setPageTitle(Crud::PAGE_EDIT, function (Utilisateur $utilisateur) {
                return 'Utilisateur : '.$utilisateur->getUsername();
            });
    }

    public function configureFields(string $pageName): iterable
    {
        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            return array_values(array_filter([
                Crud::PAGE_INDEX === $pageName ? Champ::new('imageProfil', 'Image de profil')->setTemplatePath('back/crud/field/image.html.twig') : null,
                TextField::new('username', 'Identifiant'),
                TextField::new('email', 'E-mail'),
                BooleanField::new('enabled', 'Activé'),
                DateTimeField::new('lastLogin', 'Dernière connexion'),
                ArrayField::new('roles', 'Rôles'),
            ]));
        }

        $statut = [
            $this->groupe('Image de profil', 'camera'),
            Champ::new('imageProfil', 'Image de profil')->setFormType(TextType::class),
            $this->groupe('Statut', 'user-cog'),
            Champ::new('enabled', 'Activé'),
            Champ::new('roles', 'Rôles')->setFormType(RoleType::class),
        ];

        if (Crud::PAGE_NEW === $pageName) {
            return array_merge([
                $this->onglet('Paramètres du compte'),
                Champ::new('username', 'Identifiant'),
                Champ::new('email', 'E-mail'),
                $this->groupe('Mot de passe', 'key'),
                Champ::new('plainPassword')->setFormType(RepeatedType::class)->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Répéter le mot de passe'],
                ]),
            ], $statut);
        }

        return array_merge([
            $this->onglet('Paramètres du compte'),
            Champ::new('username', 'Identifiant'),
            Champ::new('email', 'E-mail'),
            $this->groupe('Mot de passe', 'key', 'Pour changer de mot de passer, utilisez <a id="modifMDP" href="/profile/change-password">ce formulaire</a>', 'mdp'),
        ], $statut, [
            $this->onglet("Personnalisation de l'interface"),
            Champ::new('langue', "Langue de l'interface"),
            Champ::new('couleurBO', "Thème de l'interface d'administration")->setFormType(ChoiceType::class)->setFormTypeOption('choices', ['gris' => 'gris', 'bleu' => 'bleu']),
            Champ::new('blocsTableauDeBord', 'Informations présentées sur le tableau de bord')->setFormType(ChoiceType::class)->setFormTypeOptions([
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Nouvelles de version' => 'logVersion',
                    'Nombre de pages' => 'nombreDePages',
                    'Derniers commentaires' => 'derniersCommentaires',
                    'Dernières pages' => 'dernieresPages',
                    'Liste des comptes' => 'derniersInscrits',
                    'Aperçu du référencement' => 'referencement',
                    'Pages sans contenu' => 'pagesSansContenu',
                ],
            ]),
        ]);
    }

    //Le formulaire distingue le profil de l'utilisateur connecté (cf. CSS / jsBackUtilisateur.js)
    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $courant = $entityDto->getPrimaryKeyValue() == $this->getUser()->getId();
        $formOptions->set('attr.class', $courant ? 'utilisateurCourant' : 'autreUtilisateur');

        return parent::createEditFormBuilder($entityDto, $formOptions, $context);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);

        $this->addFlash('enregistrement', "Le profil de ".$entityInstance." a été enregistré");
    }
}
