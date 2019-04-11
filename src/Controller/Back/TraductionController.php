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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TraductionController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class TraductionController extends Controller
{
    /**
     * @Route("/traductions/pages", name="traductionsPages")
     * @return Response
     */
    public function traductionsPagesAction(){
        $repoPage = $this->getDoctrine()->getRepository(Page::class);
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);

        $langues = $repoLangue->findAll();
        $langue = $repoLangue->findOneBy(array('defaut' => true));
        $pages = $repoPage->findBy(array('active' => true, 'corbeille' => false, 'langue' => $langue));

        return $this->render('back/traductionsPages.html.twig', array('pages' => $pages, 'langues' => $langues, 'langueDefaut' => $langue));
    }

    /**
     * @Route("/traductions/templates", name="traductionsTemplates")
     * @param Request $request
     * @param KernelInterface $kernel
     * @return Response
     */
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

        return $this->render('back/traductionsTemplatesChoix.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/traductions/templates/modifier/{domaine}/{abreviation}", name="modifierTraductionsTemplates")
     * @param Request $request
     * @param KernelInterface $kernel
     * @return Response
     */
    public function modifierAction(Request $request, KernelInterface $kernel, $domaine, $abreviation){
        $app = $this->get('kernel')->getProjectDir();

        $abreviation = strtolower($abreviation);

        //Enregistrement via ajax
        if($request->isXmlHttpRequest()){
            $dossier = $this->getDossierByDomaine($app, $domaine);
            $fichier = $request->get('fichier');
            $segments = $request->get('segments');

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
        $repoLangue = $this->getDoctrine()->getRepository(Langue::class);
        $langue = $repoLangue->findOneBy(array('abreviation' => $abreviation));

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'translation:extract',
            'configuration' => $domaine,
            'locale' => strtolower($abreviation),
        ));

        $output = new NullOutput();
        $application->run($input, $output);

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

        return $this->render('back/traductionsTemplates.html.twig', array('fichiers' => $fichiers, 'domaine' => $domaines[$domaine], 'langue' => $langue));
    }

    private function getDossierByDomaine($app, $domaine){
        if($domaine == 'back'){
            $dossier = $app.'/translations/back';
        }else if($domaine == 'front'){
            $dossier = $app.'/translations/front';
        }else{
            $repoConfig = $this->getDoctrine()->getRepository(Configuration::class);
            $theme = $repoConfig->find(1)->getTheme();
            $dossier = $app.'/themes/'.$theme.'/translations';
        }

        return $dossier;
    }
}