<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class CommentaireCorbeilleCrudController extends CommentaireCrudController
{
    public const ENTITE = 'Commentaire_Corbeille';
    protected const CORBEILLE = true;

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Commentaire à la corbeille')
            ->setEntityLabelInPlural('Commentaires à la corbeille');
    }
}
