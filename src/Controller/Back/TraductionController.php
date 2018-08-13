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
        $app = $this->get('kernel')->getProjectDir();
        $base = dirname($app);

        $repository = $this->getDoctrine()->getRepository(Langue::class);
        $locale = $repository->findAll();

        $xml = null;
        $langueXML = null;
        $bundleXML = null;

        if($request->isMethod('GET') && isset($_GET['langue'])){
            $langueXML = $_GET['langue'];
            // si le fichier de traduction n'existe pas
            if(!file_exists($app.'/translations/messages.'.$langueXML.'.xlf')){
                // on créé le fichier messages.xx.xlf avec une ligne de commande
                $application = new Application($kernel);
                $application->setAutoExit(false);

                $input = new ArrayInput(array(
                    'command' => 'translation:update',
                    '--output-format' => 'xlf',
                    'locale' => $langueXML,
                    '--force' => true
                ));

                $output = new NullOutput();
                $application->run($input, $output);
            }

            //on ouvre le fichier de traduction
            if($document = simplexml_load_file($app.'/translations/messages.'.$langueXML.'.xlf', 'SimpleXMLElement', LIBXML_NOWARNING)){
                $document->registerXPathNamespace('u', 'urn:oasis:names:tc:xliff:document:1.2');
                $xml = $document->file->body->children();
            }else{
                $request->getSession()->getFlashBag()->add('pasDeTrad', 'Aucun message à traduire');

                return $this->render('back/traduction.html.twig', array('traductions'=>$xml, 'langues'=>$locale, 'langueXML'=>$langueXML));
            }
        }

        if($request->isMethod('POST')){
            $document = simplexml_load_file($app.'/translations/messages.'.$_POST['langueXML'].'.xlf');
            $document->registerXPathNamespace('u', 'urn:oasis:names:tc:xliff:document:1.2');
            $donnees=$_POST['traductions'];

            foreach($donnees as $id => $nouvelletraduction){
                $xpath = "//u:trans-unit[@id='".$id."']";
                $anciennetraduction = $document->xpath($xpath);
                $anciennetraduction[0]->target = $nouvelletraduction;
            }

            if($document->asXML($app.'/translations/messages.'.$_POST['langueXML'].'.xlf')){
                $request->getSession()->getFlashBag()->add('tradOK', 'La traduction a bien été enregistrée');
            }

        }

        return $this->render('back/traduction.html.twig', array('traductions'=>$xml, 'langues'=>$locale, 'langueXML'=>$langueXML));
    }
}