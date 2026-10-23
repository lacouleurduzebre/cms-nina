<?php

namespace App\Controller\Admin;

use App\Admin\Field\Champ;
use App\Entity\Configuration;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ConfigurationCrudController extends BaseCrudController
{
    public const ENTITE = 'Configuration';
    protected const CHAMPS_THEME_CMS = ['logo', 'favicon'];

    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    protected function controleAcces(string $action, $entite = null): void
    {
        if (!$this->droits->checkDroit('config')) {
            $this->refuserAcces();
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Configuration')
            ->setEntityLabelInPlural('Configurations')
            ->setSearchFields(null);
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->remove(Crud::PAGE_EDIT, Action::DELETE)
            ->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            return [
                Champ::new('logo')->setTemplatePath('back/crud/field/image.html.twig'),
                TextField::new('nom', 'Nom du site'),
                TextField::new('theme'),
                TextField::new('editeur', 'Éditeur'),
            ];
        }

        return [
            $this->groupe('Identité', 'copyright'),
            Champ::new('nom', 'Nom du site'),
            Champ::new('logo')->setFormType(TextType::class),
            Champ::new('favicon')->setFormType(TextType::class)->setHelp('privilégier les fichiers png au format 32px par 32px'),
            $this->groupe('Contact', 'at'),
            Champ::new('editeur', 'Éditeur'),
            Champ::new('emailContact'),
            Champ::new('emailMaintenance'),
            $this->groupe('Publication', 'newspaper'),
            Champ::new('affichageCommentaires', 'Autoriser les commentaires sur les nouvelles pages'),
            Champ::new('affichageDatePublication', 'Afficher la date de publication sur les nouvelles pages'),
            Champ::new('affichageAuteur', "Afficher l'auteur sur les nouvelles pages"),
            $this->groupe('Cookies', 'cookie-bite'),
            Champ::new('bandeauCookies', "Afficher le bandeau informant l'utilisateur de l'enregistrement de cookies"),
            Champ::new('pageCookies', 'Page contenant les informations sur les cookies')->setHelp('(ex : mentions légales)'),
            $this->groupe('Développement', 'cog'),
            Champ::new('analytics', 'Script Google Analytics')->setFormTypeOption('attr', ['class' => 'notTinymce']),
            Champ::new('nbArticlesFluxRSS', 'Nombre de pages à afficher dans le flux rss'),
            Champ::new('maintenance', 'Mode maintenance')->setHelp('Si le mode maintenance est activé, votre site ne sera pas accessible aux utilisateurs anonymes'),
        ];
    }
}
