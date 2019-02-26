<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 26/02/2019
 * Time: 15:28
 */

namespace App\Controller\Back\Admin;


use App\Controller\Back\AdminController;
use App\Entity\Page;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class LangueAdminController extends AdminController
{
    //Suppression d'une langue : réinitialisation cookie langueArbo
    protected function removeLangueEntity($entity){
        $this::removeEntity($entity);

        if (isset($_COOKIE['langueArbo'])) {
            unset($_COOKIE['langueArbo']);
            setcookie('langueArbo', '', time() - 3600, '/');
        }
    }

    //Édition d'une langue : seule les pages dans cette langue peuvent être choisies comme page d'accueil
    protected function createLangueEntityFormBuilder($entity, $view){
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        if($view == "edit" && $formBuilder->getData()) {

            $langue = $formBuilder->getData();

            $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($langue) {
                $event->getForm()->add('pageAccueil', EntityType::class, [
                    'required' => false,
                    'class' => Page::class,
                    'choice_label' => 'titreMenu',
                    'query_builder' => function (EntityRepository $er) use ($langue) {
                        return $er->createQueryBuilder('p')
                            ->andWhere('p.langue = :langue')
                            ->setParameters(array('langue' => $langue))
                            ->orderBy('p.titreMenu', 'ASC');
                    }
                ]);

            });
        }
        return $formBuilder;
    }
}