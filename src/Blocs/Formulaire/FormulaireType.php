<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\Formulaire;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('destinataires', CollectionType::class, array(
                'entry_type' => TextType::class,
                'entry_options' => array(
                    'label' => false
                ),
                'label' => 'Destinataires',
                'label_format' => 'destinataire',
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('objet', TextType::class, array(
                'label' => "Objet du mail envoyé"
            ))
            ->add('messageConfirmation', TextareaType::class, array(
                'label' => "Message affiché après l'envoi du mail"
            ))
            ->add('champs', CollectionType::class, array(
                'entry_type' => ChampType::class,
                'entry_options' => array(
                    'label' => false
                ),
                'label' => 'Champs',
                'label_format' => 'champ',
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('submit', TextType::class, array(
                'label' => "Texte du bouton d'envoi",
                'help' => "Par défaut : \"Envoyer\""
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

    public function getParent(){
        return FormType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        usort($view['champs']->children, function (FormView $a, FormView $b) {
            $objectA = $a->vars['data'];
            $objectB = $b->vars['data'];

            $posA = $objectA['position'];
            $posB = $objectB['position'];

            if ($posA == $posB) {
                return 0;
            }

            return ($posA < $posB) ? -1 : 1;
        });
    }
}