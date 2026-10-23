<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/07/2018
 * Time: 14:47
 */

namespace App\Controller\Back;


use App\Service\Droits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * Class BlocController
 * @package App\Controller\Back
 */
#[Route(path: '/admin')]
class BlocController extends AbstractController
{
    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/bloc/ajouterBloc', name: 'ajouterBloc')]
    public function ajouterBlocAction(Request $request){
        if($request->isXmlHttpRequest()){
            $type = $request->request->get('type');
            $typeBloc = $request->request->get('typeBloc');

            $form = $this->createForm("App\Form\Type\\".$typeBloc."Type", null, array('type' => $type, 'block_name' => $type));
            return $this->render('back/blocs/formulaire.html.twig', array('form'=>$form->createView()));
        };

        return false;
    }

    /**
     * @param Request $request
     * @return bool|Response
     */
    #[Route(path: '/bloc/configuration', name: 'configurationBlocs')]
    public function ConfigurationBlocsAction(Request $request, Droits $droits){
        if(!$droits->checkDroit('configBlocs')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        if($request->isXmlHttpRequest()){
            $action = $request->query->get('action');
            if($action == 'actif'){
                $type = $request->query->get('type');
                $actif = $request->query->get('actif');

                $infos = Yaml::parseFile('../src/Blocs/configBlocs.yaml');

                ($actif == 'true') ? $infos[$type]['actif'] = 'oui' : $infos[$type]['actif'] = 'non';

                $nvConfig = Yaml::dump($infos);

                file_put_contents('../src/Blocs/configBlocs.yaml', $nvConfig);

                return new Response ('ok');
            }else if($action == 'priorite'){
                $blocs = $request->query->all('blocs');

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

            return $this->render('back/blocs/configuration.html.twig', array('blocs'=>$blocsContenu, 'blocsAnnexes'=>$blocsAnnexes, 'jsPartiel' => 'ConfigBloc'));
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
}