<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/07/2018
 * Time: 14:47
 */

namespace App\Controller\Back;


use App\Entity\Bloc;
use App\Service\Droits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * Class BlocController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class BlocController extends AbstractController
{
    /**
     * @Route("/bloc/ajouterBloc", name="ajouterBloc")
     * @param Request $request
     * @return bool|Response
     */
    public function ajouterBlocAction(Request $request){
        if($request->isXmlHttpRequest()){
            $type = $request->get('type');
            $typeBloc = $request->get('typeBloc');

            $form = $this->get('form.factory')->createBuilder("App\Form\Type\\".$typeBloc."Type", null, array('type' => $type, 'block_name' => $type))->getForm();
            return $this->render('back/blocs/formulaire.html.twig', array('form'=>$form->createView()));
        };

        return false;
    }

    /**
     * @Route("/bloc/apercuBloc", name="apercuBloc")
     * @param Request $request
     * @return bool|Response
     */
    public function apercuBlocAction(Request $request, Environment $twig){
        if($request->isXmlHttpRequest()){
            $idBloc = $request->get('idBloc');
            $typeBloc = $request->get('typeBloc');
            $champs = $request->get('contenu');

            $contenu = [];
            parse_str($champs, $contenu);
            $contenu = $this->getArray($contenu, 'contenu');

            if(isset($idBloc)){
                $bloc = $this->getDoctrine()->getRepository(Bloc::class)->find($idBloc);
            }else{
                $bloc = new Bloc();
                $bloc->setType($typeBloc);
            }

            $bloc->setContenu($contenu);

            if($twig->getLoader()->exists('/Blocs/'.$bloc->getType().'/bloc-'.$bloc->getId().'.html.twig')){
                $tpl = $this->render('/Blocs/'.$bloc->getType().'/bloc-'.$bloc->getId().'.html.twig', ['bloc' => $bloc]);
            }else{
                $tpl = $this->render('/Blocs/'.$bloc->getType().'/'.$bloc->getType().'.html.twig', ['bloc' => $bloc]);
            }

            return $tpl;
        };

        return false;
    }

    /**
     * @Route("/bloc/configuration", name="configurationBlocs")
     * @param Request $request
     * @return bool|Response
     */
    public function ConfigurationBlocsAction(Request $request, Droits $droits){
        if(!$droits->checkDroit('configBlocs')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        if($request->isXmlHttpRequest()){
            $action = $request->get('action');
            if($action == 'actif'){
                $type = $request->get('type');
                $actif = $request->get('actif');

                $infos = Yaml::parseFile('../src/Blocs/configBlocs.yaml');

                ($actif == 'true') ? $infos[$type]['actif'] = 'oui' : $infos[$type]['actif'] = 'non';

                $nvConfig = Yaml::dump($infos);

                file_put_contents('../src/Blocs/configBlocs.yaml', $nvConfig);

                return new Response ('ok');
            }else if($action == 'priorite'){
                $blocs = $request->get('blocs');

                foreach($blocs as $type => $priorite){
                    $infos = Yaml::parseFile('../src/Blocs/configBlocs.yaml');
                    if($infos[$type]['priorite'] != $priorite){
                        $infos[$type]['priorite'] = (int)$priorite;

                        $nvInfos = Yaml::dump($infos);

                        file_put_contents('../src/Blocs/configBlocs.yaml', $nvInfos);
                    }
                }

                return new Response('ok');
            }else{
                return false;
            }
        }else{
            $blocs = $this->getInfosBlocs();

            $blocsContenu = $blocs['contenu'];
            $blocsAnnexes = $blocs['annexe'];
            $entityConfig = ['name' => 'ConfigBloc'];

            return $this->render('back/blocs/configuration.html.twig', array('blocs'=>$blocsContenu, 'blocsAnnexes'=>$blocsAnnexes, '_entity_config'=>$entityConfig));
        }
    }

    public function getInfosBlocs(){
        $types = scandir('../src/Blocs');
        $types = array_combine(array_values($types), array_values($types));
        unset($types["."]);
        unset($types[".."]);
        unset($types["configBlocs.yaml"]);

        $blocs = [];
        $blocsContenu = [];
        $blocsAnnexes = [];
        $config = Yaml::parseFile('../src/Blocs/configBlocs.yaml');
        foreach($types as $type){
            if(file_exists('../src/Blocs/'.$type.'/infos.yaml')){
                $infos = Yaml::parseFile('../src/Blocs/'.$type.'/infos.yaml');
                $infos['identifiant'] = $type;
                $infos['priorite'] = $config[$type]['priorite'];
                $infos['actif'] = $config[$type]['actif'];
                if($infos['type'] == 'contenu'){
                    $blocsContenu[$type] = $infos;
                }else{
                    $blocsAnnexes[$type] = $infos;
                }
            }
        }

        $blocs['contenu'] = $blocsContenu;
        $blocs['annexe'] = $blocsAnnexes;

        return $blocs;
    }

    public function getArray($array, $index) {
        $queue = array($array);
        while (($item = array_shift($queue)) !== null) {
            if (!is_array($item)) continue;
            if (isset($item[$index])) return $item[$index];
            $queue = array_merge($queue, $item);
        }
        return null;
    }
}