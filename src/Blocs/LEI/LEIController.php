<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 20/08/2018
 * Time: 14:22
 */

namespace App\Blocs\LEI;


use App\Entity\Bloc;
use App\Service\Langue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class LEIController extends Controller
{
    /**
     * @Route("/fiche/{url}/{idFiche}/{idBloc}", name="voirFicheLEI")
     * @Route("/{_locale}/fiche/{url}/{idFiche}/{idBloc}", name="voirFicheLEILocale", requirements={
     *     "_locale"="^[A-Za-z]{1,2}$"
     * })
     * @param Request $request
     * @param $idFiche
     * @param $url
     * @param $idModule
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voirFicheLEIAction($url, $idFiche, $idBloc, Langue $slangue, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('voirFicheLEI', $_locale, array('url' => $url, 'idFiche' => $idFiche, 'idBloc' => $idBloc));
        if($redirection){
            return $redirection;
        }

        $repoBloc = $this->getDoctrine()->getRepository(Bloc::class);
        $bloc = $repoBloc->find($idBloc);

        //Utilisation du cache si dispo
        $cache = '../src/Blocs/LEI/cache/cache'.$idBloc.'.xml';

        if(file_exists($cache)){
            $xml = simplexml_load_file($cache);
        }else{
            //Utilisation du flux générique ou du flux spécifique
            $parametres = $bloc->getContenu();

            if(array_key_exists('utiliserFluxSpecifique', $parametres) && isset($parametres['utiliserFluxSpecifique'][0])){
                $flux = $parametres['flux'];
            }else{
                $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
                $flux = $configLEI['fluxGenerique'];
            }

            //Ajout de la clause et des autres paramètres
            if(array_key_exists('clause', $parametres)){
                $flux .= '&clause='.$parametres['clause'];
            }
            if(array_key_exists('autresParametres', $parametres)){
                $flux .= $parametres['autresParametres'];
            }

            //Création du fichier de cache
            $file_headers = @get_headers($flux);
            if($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
                copy($flux, $cache);
            }

            $xml = simplexml_load_file($cache);
        }

        $fiche = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]");

        if(!$fiche){
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }

        //Navigation
        $noeudFichePrecedente = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]/preceding-sibling::sit_liste[position()=1]");
        $fichePrecedente = $noeudFichePrecedente ? ['PRODUIT' => (string)$noeudFichePrecedente[0]->PRODUIT, 'NOM' => (string)$noeudFichePrecedente[0]->NOM] : false;

        $noeudFicheSuivante = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]/following-sibling::sit_liste[position()=1]");
        $ficheSuivante = $noeudFicheSuivante ? ['PRODUIT' => (string)$noeudFicheSuivante[0]->PRODUIT, 'NOM' => (string)$noeudFicheSuivante[0]->NOM] : false;

        $liste = $bloc->getPage();

        //Infos sur la fiche
        $infosFiche = [];

        $criteres = $fiche[0]->CRITERES->Crit;
        foreach($criteres as $critere){
            $clef_critere = (string)$critere->attributes()->CLEF_CRITERE;
            $clef_moda = (int)$critere->attributes()->CLEF_MODA;

            $legende = $xml->xpath("//NOMENCLATURE/CRIT[@CLEF=$clef_critere]")[0];

            $classe = (string)$legende->attributes()->CLASSE;
            $nomCritere = (string)$legende->NOMCRIT;
            $moda = $legende->xpath("MODAL[@CLEF=$clef_moda]");

            if($moda){
                $texte = $moda[0]->__toString();
                $infosFiche[$classe][$nomCritere] = [
                    'texte' => $texte,
                    'critere' => $clef_critere,
                    'moda' => $clef_moda
                ];
            }
        }

        return $this->render('Blocs/LEI/fiche.html.twig', array('fiche' => $fiche[0], 'fichePrecedente' => $fichePrecedente, 'ficheSuivante' => $ficheSuivante, 'liste' => $liste, 'infosFiche' => $infosFiche));
    }

    /**
     * @Route("/admin/LEI/viderCache", name="viderCacheLEI")
     */
    public function viderCacheLEI(){
        $files = glob('../src/Blocs/LEI/cache/*');
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }

        return new Response('ok');
    }

    /**
     * @Route("/admin/LEI/modifierPictos", name="modifierPictosLEI")
     */
    public function modifierPictosLEI(Request $request){
        //Pictos enregistrés
        $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
        $pictos = [];
        $pictos['pictos'] = $configLEI['pictos'];

        //Formulaire
        $form = $this->createFormBuilder($pictos)
            ->add('pictos', CollectionType::class, [
                'entry_type' => PictoType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'required' => false
            ])
            ->add('Enregistrer', SubmitType::class)
            ->getForm();

        //Enregistrement
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $nvPictos = $form->getData()['pictos'];

            $configLEI['pictos'] = $nvPictos;

            $nvFichier = Yaml::dump($configLEI);
            file_put_contents('../src/Blocs/LEI/configLEI.yaml', $nvFichier);

            $this->addFlash('enregistrement', 'Les pictogrammes ont été enregistrés');
        }

        return $this->render('Blocs/LEI/configPictos.html.twig', ['form' => $form->createView()]);
    }
}