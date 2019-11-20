<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\BlocPartage;


use App\Entity\BlocPartage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlocPartageType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
     $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoBlocsPartages = $this->em->getRepository(BlocPartage::class);
        $objetsBlocsPartages = $repoBlocsPartages->findAll();
        $blocsPartages = [];
        foreach($objetsBlocsPartages as $objetBlocPartage){
            $blocsPartages[$objetBlocPartage->getNom()] = $objetBlocPartage->getBloc()->getId();
        }

        $builder
            ->add('blocPartage', ChoiceType::class, array(
                'choices' => $blocsPartages,
                'label' => 'Bloc'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    public function getParent(){
        return FormType::class;
    }
}