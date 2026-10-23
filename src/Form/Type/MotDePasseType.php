<?php

namespace App\Form\Type;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Changement (avec le mot de passe actuel) ou réinitialisation du mot de passe
 */
class MotDePasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['mot_de_passe_actuel']) {
            $builder->add('motDePasseActuel', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new UserPassword(message: 'Le mot de passe est invalide.'),
                ],
                'attr' => ['autocomplete' => 'current-password'],
            ]);
        }

        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => ['label' => 'Nouveau mot de passe', 'attr' => ['autocomplete' => 'new-password']],
            'second_options' => ['label' => 'Répéter le nouveau mot de passe', 'attr' => ['autocomplete' => 'new-password']],
            'invalid_message' => 'Les deux mots de passe ne sont pas identiques.',
            'constraints' => [
                new NotBlank(message: 'Entrez un nouveau mot de passe s\'il vous plait.'),
                new Length(min: 2, max: 4096, minMessage: 'Le nouveau mot de passe est trop court.'),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'mot_de_passe_actuel' => false,
        ]);
        $resolver->setAllowedTypes('mot_de_passe_actuel', 'bool');
    }
}
