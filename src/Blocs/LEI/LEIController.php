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
use Psr\SimpleCache\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class LEIController extends AbstractController
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
    public function voirFicheLEIAction($url, $idFiche, $idBloc, Langue $slangue, Request $request, CacheInterface $cache, $_locale = null){
        //Test route : locale ou non
        $redirection = $slangue->redirectionLocale('voirFicheLEI', $_locale, array('url' => $url, 'idFiche' => $idFiche, 'idBloc' => $idBloc));
        if($redirection){
            return $redirection;
        }

        $repoBloc = $this->getDoctrine()->getRepository(Bloc::class);
        $bloc = $repoBloc->find($idBloc);

        //Langue
        $repoLangue = $this->getDoctrine()->getRepository(\App\Entity\Langue::class);
        $locale = $request->getLocale();
        if($locale){
            $langue = $repoLangue->findOneBy(array('abreviation'=>$locale));
        }
        if(!$locale || !$langue){
            $langue = $repoLangue->findOneBy(array('defaut'=>1));
        }
        //Fin langue

        //Utilisation du cache si dispo
        $cleCache = 'LEI_'.$idBloc.'_'.$langue->getAbreviation();

        if($_ENV['APP_ENV'] == 'prod'){
            $flux = $cache->get($cleCache);
        }

        if(!isset($flux)){
            //Utilisation du flux générique ou du flux spécifique
            $parametres = $bloc->getContenu();

            if(array_key_exists('utiliserFluxSpecifique', $parametres) && isset($parametres['utiliserFluxSpecifique'][0])){
                $urlFlux = $parametres['flux'];
            }else{
                $configLEI = Yaml::parseFile('../src/Blocs/LEI/configLEI.yaml');
                $urlFlux = $configLEI['fluxGenerique'];
            }

            //Ajout de la clause et des autres paramètres
            if(array_key_exists('clause', $parametres)){
                $urlFlux .= '&clause='.$parametres['clause'];
            }
            if(array_key_exists('autresParametres', $parametres)){
                $urlFlux .= $parametres['autresParametres'];
            }

            //Création du fichier de cache
            $file_headers = @get_headers($urlFlux);
            if($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
                $flux = file_get_contents($urlFlux);
                if($_ENV['APP_ENV'] == 'prod') {
                    $cache->set($cleCache, $flux, 86400);
                }
            }
        }

        $xml = simplexml_load_string($flux);
        $fiche = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]");

        if(!$fiche){
            throw new NotFoundHttpException('Cette page n\'existe pas ou a été supprimée');
        }

        //Navigation
        $noeudFichePrecedente = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]/preceding-sibling::sit_liste[position()=1]");
        $fichePrecedente = $noeudFichePrecedente ? ['PRODUIT' => (string)$noeudFichePrecedente[0]->PRODUIT, 'NOM' => (string)$noeudFichePrecedente[0]->NOM] : false;

        $noeudFicheSuivante = $xml->xpath("//Resultat/sit_liste[PRODUIT = $idFiche]/following-sibling::sit_liste[position()=1]");
        $ficheSuivante = $noeudFicheSuivante ? ['PRODUIT' => (string)$noeudFicheSuivante[0]->PRODUIT, 'NOM' => (string)$noeudFicheSuivante[0]->NOM] : false;

        //Liste
        $liste = $bloc->getPage();
        if(is_null($liste)){
            //Groupe de blocs -> Accueil
            if(!is_null($bloc->getGroupeBlocs())){
                $repoLangue = $this->getDoctrine()->getRepository(\App\Entity\Langue::class);
                $locale = $request->getLocale();
                if($locale){
                    $langue = $repoLangue->findOneBy(array('abreviation'=>$locale));
                }
                if(!$locale || !$langue){
                    $langue = $repoLangue->findOneBy(array('defaut'=>1));
                }
                $liste = $langue->getPageAccueil();
            }else{//Blocs parents
                while($bloc->getBlocParent()){
                    $bloc = $bloc->getBlocParent();
                }
                $liste = $bloc->getPage();
            }
        }

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
                foreach($moda as $element){
                    $texte = $element->__toString();
                    if($critere->__toString()){
                        $texte .= ' : '.$critere->__toString();
                    }
                    $infosFiche[$classe][$nomCritere][] = [
                        'texte' => $texte,
                        'critere' => $clef_critere,
                        'moda' => $clef_moda
                    ];
                }
            }else{
                $texte = $critere->__toString();
                $infosFiche[$classe][$nomCritere][] = [
                    'texte' => $texte,
                    'critere' => $clef_critere,
                    'moda' => $clef_moda
                ];
            }
        }

        return $this->render('Blocs/LEI/fiche.html.twig', array('fiche' => $fiche[0], 'fichePrecedente' => $fichePrecedente, 'ficheSuivante' => $ficheSuivante, 'liste' => $liste, 'infosFiche' => $infosFiche));
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