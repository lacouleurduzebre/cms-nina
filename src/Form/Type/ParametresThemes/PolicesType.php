<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2020-11-03
 * Time: 10:50
 */

namespace App\Form\Type\ParametresThemes;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PolicesType extends AbstractType
{
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $requetePolices = $this->client->request(
            'GET',
            'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBn3F7GSk-BIhe2FgjyLmVM26yEg-nE7CI'
        );

        $polices = [];
        if($requetePolices->getStatusCode() == '200') {
            $googleFonts = $requetePolices->toArray();
            foreach($googleFonts['items'] as $font){
                if(in_array('latin-ext', $font['subsets'])){
                    $polices[$font['family']] = $font['family'];
                }
            }
        }

        $resolver->setDefaults(array(
            'choices' => $polices,
            'multiple' => true,
            'attr' => [
                'data-widget' => 'select2'
            ]
        ));
    }

    public function getParent(){
        return ChoiceType::class;
    }
}