<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 11/12/2017
 * Time: 10:20
 */

namespace App\Controller\Back;


use App\Entity\Configuration;
use App\Entity\Langue;
use App\Entity\Page;
use App\Service\ExtractionTraductions;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TraductionController
 * @package App\Controller\Back
 */
#[Route(path: '/admin')]
class TraductionController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return Response
     */
    #[Route(path: '/traductions/pages', name: 'traductionsPages')]
    public function traductionsPagesAction(){
        $repoPage = $this->doctrine->getRepository(Page::class);
        $repoLangue = $this->doctrine->getRepository(Langue::class);

        $langues = $repoLangue->findAll();
        uasort ($langues, function($a, $b) {
            if($a->getDefaut() === $b->getDefaut()) {
                return 0;
            }
            return $a->getDefaut() > $b->getDefaut() ? -1 : 1;
        });
        $langue = $repoLangue->findOneBy(array('defaut' => true));
        $pages = $repoPage->findBy(array('corbeille' => false));

        return $this->render('back/traductionsPages.html.twig', array('pages' => $pages, 'langues' => $langues, 'langueDefaut' => $langue, 'jsPartiel' => 'Traduction'));
    }

    /**
     * @param Request $request
     * @param KernelInterface $kernel
     * @return Response
     */
    #[Route(path: '/traductions/templates', name: 'traductionsTemplates')]
    public function traductionsTemplatesAction(Request $request, KernelInterface $kernel){
        //Vider le cache
        if($request->isXmlHttpRequest()){
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput(array(
                'command' => 'cache:clear'
            ));

            $output = new NullOutput();
            $application->run($input, $output);

            return new Response('ok');
        }

        //Choix de la langue et du domaine
        $formChoix = $this->createFormBuilder();
        $formChoix
            ->add('domaine', ChoiceType::class, array(
                'label' => 'Modifier les traductions du ',
                'choices' => array(
                    'Back-Office' => 'back',
                    'Front-Office' => 'front',
                    'Thème actif' => 'theme'
                ),
                'required' => true
            ))
            ->add('langue', EntityType::class, array(
                'label' => 'en ',
                'class' => Langue::class,
                'required' => true
            ))
            ->add('Traduire', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn'
                )
            ));

        $form = $formChoix->getForm();

        //Redirection vers la liste des segments à traduire si le choix de la langue et du domaine a été fait
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->redirectToRoute('modifierTraductionsTemplates', array('domaine' => $data['domaine'], 'abreviation' => $data['langue']->getAbreviation()));
        }

        return $this->render('back/traductionsTemplatesChoix.html.twig', array('form' => $form->createView(), 'jsPartiel' => 'Traduction'));
    }

    /**
     * @param Request $request
     * @param ExtractionTraductions $extraction
     * @return Response
     */
    #[Route(path: '/traductions/templates/modifier/{domaine}/{abreviation}', name: 'modifierTraductionsTemplates')]
    public function modifierAction(Request $request, ExtractionTraductions $extraction, $domaine, $abreviation){
        $app = getcwd().'/../';

        $abreviation = strtolower($abreviation);

        //Enregistrement via ajax
        if($request->isXmlHttpRequest()){
            $dossier = $this->getDossierByDomaine($app, $domaine);
            $fichier = $request->request->get('fichier');
            $segments = $request->request->all('segments');

            $document = simplexml_load_file($dossier.'/'.$fichier.'.'.strtolower($abreviation).'.xlf');
            $document->registerXPathNamespace('u', 'urn:oasis:names:tc:xliff:document:2.0');

            foreach($segments as $segment){
                $anciennetraduction = $document->xpath("//u:unit[@id='".$segment['name']."']");
                $anciennetraduction[0]->segment->target = $segment['value'];
            }

            if($document->asXML($dossier.'/'.$fichier.'.'.strtolower($abreviation).'.xlf')){
                return new Response('ok');
            }

            return new Response('pas ok');
        }

        $domaines = array(
            'back' => 'back-office',
            'front' => 'front-office',
            'theme' => 'thème actif'
        );

        //Ligne de commande générant les fichiers de traduction
        $repoLangue = $this->doctrine->getRepository(Langue::class);
        $langue = $repoLangue->findOneBy(array('abreviation' => $abreviation));

        $theme = $this->doctrine->getRepository(Configuration::class)->find(1)->getTheme();
        $extraction->extraire($domaine, strtolower($abreviation), $theme);

        //Recherche du dossier contenant les traductions, en fonction du domaine
        $dossier = $this->getDossierByDomaine($app, $domaine);

        //Ouverture de tous les fichiers du dossier
        $files = glob($dossier.'/*.'.$abreviation.'.{xlf}', GLOB_BRACE);
        $fichiers = [];
        foreach($files as $file) {
            if($document = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOWARNING)){
                $document->registerXPathNamespace('u', 'urn:oasis:names:tc:xliff:document:2.0');
                $xml = $document->file;
                $fichiers[basename($file, '.'.$abreviation.'.xlf')] = $xml;
            }
        }

        return $this->render('back/traductionsTemplates.html.twig', array('fichiers' => $fichiers, 'domaine' => $domaines[$domaine], 'langue' => $langue, 'jsPartiel' => 'Traduction'));
    }

    private function getDossierByDomaine($app, $domaine){
        if($domaine == 'back'){
            $dossier = $app.'/translations/back';
        }else if($domaine == 'front'){
            $dossier = $app.'/translations/front';
        }else{
            $repoConfig = $this->doctrine->getRepository(Configuration::class);
            $theme = $repoConfig->find(1)->getTheme();
            $dossier = $app.'/themes/'.$theme.'/translations';
        }

        return $dossier;
    }
}