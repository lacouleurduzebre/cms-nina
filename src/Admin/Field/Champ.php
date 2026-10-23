<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

/**
 * Champ de formulaire du back-office, rendu comme avec EasyAdmin 2 : sans type de formulaire,
 * Symfony le devine à partir du mapping Doctrine ; avec un type (blocs, SEO, rôles...), il est utilisé tel quel.
 * (Le champ générique d'EasyAdmin 3 ne gère pas les associations et ajoute ses propres widgets.)
 */
final class Champ implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/text')
            ->setDefaultColumns('col-12');
    }
}
