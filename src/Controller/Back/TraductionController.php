<?php
/**
 * Created by PhpStorm.
 * User: nadegehamann
 * Date: 11/12/2017
 * Time: 10:20
 */

namespace App\Controller\Back;


use App\Entity\Langue;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class TraductionController extends Controller
{
    /**
     * @Route("/admin/traductions/modifier", name="modifierTraductions")
     * @param Request $request
     * @param KernelInterface $kernel
     * @return Response
     */
    public function modifierAction(Request $request, KernelInterface $kernel){
        $app = $this->get('kernel')->getRootDir();

        $base = dirname($app);
        $dossierBundle = dir($base.'/src/Nina');
        $bundles = array();
        while ($fichier = $dossierBundle->read()){
            if(strlen($fichier) > 2) $bundles[] = substr($fichier, 0, -6);
        }
        $dossierBundle->close();

        /*$dossierLangue = dir($app.'/Resources/NinaFrontBundle/translations');
        $locale = array();
        while ($fichier = $dossierLangue->read()){
            $langue = substr($fichier, 9, 2);
            if($langue && !substr($fichier, 15, 1)) $locale[]=$langue;
        }
        $locale = array_unique($locale);
        $dossierLangue->close();*/

        $repository = $this->getDoctrine()->getRepository(Langue::class);
        $locale = $repository->findAll();

        $xml = null;
        $langueXML = null;
        $bundleXML = null;

        if($request->isMethod('GET')){
            if($_GET){
                $langueXML = $_GET['langue'];
                $bundleXML = $_GET['bundle'];
                // si le fichier de traduction n'existe pas
                if(!file_exists($app.'/Resources/Nina'.$bundleXML.'Bundle/translations/messages.'.$langueXML.'.xlf')){
                    $dossierTraductions = $base.'/app/Resources/Nina'.$bundleXML.'Bundle';

                    // on créé le dossier de traductions s'il n'existe pas
                    if(file_exists($dossierTraductions) == false){
                        mkdir($dossierTraductions);
                        mkdir($dossierTraductions.'/translations');
                    }

                    // on créé le fichier messages.xx.xlf avec une ligne de commande
                    $application = new Application($kernel);
                    $application->setAutoExit(false);

                    $input = new ArrayInput(array(
                        'command' => 'translation:update',
                        '--output-format' => 'xlf',
                        'locale' => $langueXML,
                        'bundle' => 'Nina'.$bundleXML.'Bundle',
                        '--force' => true
                    ));

                    $output = new NullOutput();
                    $application->run($input, $output);
                }

                //on ouvre le fichier de traduction
                if($document = simplexml_load_file($app.'/Resources/Nina'.$bundleXML.'Bundle/translations/messages.'.$langueXML.'.xlf', 'SimpleXMLElement', LIBXML_NOWARNING)){
                    $document->registerXPathNamespace('u', 'urn:oasis:names:tc:xliff:document:1.2');
                    $xml = $document->file->body->children();
                }else{
                    $request->getSession()->getFlashBag()->add('pasDeTrad', 'Aucun message à traduire');

                    return $this->render('back:traduction:traduction.html.twig', array('traductions'=>$xml, 'bundles'=>$bundles, 'langues'=>$locale, 'bundleXML'=>$bundleXML, 'langueXML'=>$langueXML));
                }

            }
        }

        if($request->isMethod('POST')){
            $document = simplexml_load_file($app.'/Resources/Nina'.$_POST['bundleXML'].'Bundle/translations/messages.'.$_POST['langueXML'].'.xlf');
            $document->registerXPathNamespace('u', 'urn:oasis:names:tc:xliff:document:1.2');
            $donnees=$_POST['traductions'];

            foreach($donnees as $id => $nouvelletraduction){
                $xpath = "//u:trans-unit[@id='".$id."']";
                $anciennetraduction = $document->xpath($xpath);
                $anciennetraduction[0]->target = $nouvelletraduction;
            }

            if($document->asXML($app.'/Resources/Nina'.$_POST['bundleXML'].'Bundle/translations/messages.'.$_POST['langueXML'].'.xlf')){
                $request->getSession()->getFlashBag()->add('tradOK', 'La traduction a bien été enregistrée');
            }

        }

        return $this->render('back:traduction:traduction.html.twig', array('traductions'=>$xml, 'bundles'=>$bundles, 'langues'=>$locale, 'bundleXML'=>$bundleXML, 'langueXML'=>$langueXML));
    }
}