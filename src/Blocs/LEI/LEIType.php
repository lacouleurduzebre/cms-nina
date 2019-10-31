<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 13/07/2018
 * Time: 11:45
 */

namespace App\Blocs\LEI;


use App\Form\Type\LimiteType;
use App\Form\Type\PaginationType;
use App\Form\Type\ResultatsParPageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class LEIType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
        $fluxGenerique = $configLEI['fluxGenerique'];

        $builder
            ->add('fluxGenerique', TextType::class, array(
                'mapped' => false,
                'label' => 'Flux générique',
                'help' => 'Commun à tous les blocs LEI',
                'data' => $fluxGenerique
            ))
            ->add('utiliserFluxSpecifique', ChoiceType::class, [
                'choices' => [
                    'Utiliser un flux spécifique' => 1
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => false
            ])
            ->add('flux', TextType::class, array(
                'label' => 'Flux spécifique',
            ))
            ->add('clause', TextType::class)
            ->add('autresParametres', TextType::class, [
                'label' => 'Autres paramètres'
            ])
            ->add('clef_moda', TextType::class, array(
                'label' => 'Limiter à la clé de modalité :',
                'help' => "Filtrer les résultats pour ne conserver que les fiches répondant à ce critère",
                'required' => false
            ))
            ->add('limite', LimiteType::class)
            ->add('pagination', PaginationType::class)
            ->add('resultatsParPage', ResultatsParPageType::class);

        //Enregistrement du flux générique dans le fichier de config LEI
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $blocLEI = $event->getData();
            $fluxGenerique = $blocLEI['fluxGenerique'];

            $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
            $configLEI['fluxGenerique'] = $fluxGenerique;
            $nvFichier = Yaml::dump($configLEI);
            file_put_contents('../src/Blocs/LEI/configLEI.yaml', $nvFichier);
        });

        //Enregistrement du fichier de cache
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $bloc = $event->getForm()->getParent()->getData();
            $contenuBloc = $event->getData();

            //Utilisation du flux générique ou du flux spécifique
            if($contenuBloc['utiliserFluxSpecifique'] && isset($contenuBloc['utiliserFluxSpecifique'][0])){
                $flux = $contenuBloc['flux'];
            }else{
                $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
                $flux = $configLEI['fluxGenerique'];
            }

            //Ajout de la clause et des autres paramètres
            if(isset($contenuBloc['clause'])){
                $flux .= '&clause='.$contenuBloc['clause'];
            }
            if(isset($contenuBloc['autresParametres'])){
                $flux .= $contenuBloc['autresParametres'];
            }

            //Enregistrement du fichier
            $fichier = '../src/Blocs/LEI/cache/cache'.$bloc->getId().'.xml';

            if(file_exists($fichier)){
                unlink($fichier);
            }

            $file_headers = @get_headers($flux);
            if($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
                copy($flux, $fichier);
            }
        });
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
}