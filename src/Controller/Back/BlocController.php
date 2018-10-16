<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/07/2018
 * Time: 14:47
 */

namespace App\Controller\Back;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class BlocController extends Controller
{
    /**
     * @Route("/admin/bloc/ajouterBloc", name="ajouterBloc")
     * @param Request $request
     * @return bool|Response
     */
    public function ajouterBlocAction(Request $request){
        if($request->isXmlHttpRequest()){
            $type = $request->get('type');
            $typeBloc = $request->get('typeBloc');

            $form = $this->get('form.factory')->createBuilder("App\Form\Type\\".$typeBloc."Type", null, array('type' => $type))->getForm();
            return $this->render('back/blocs/formulaire.html.twig', array('form'=>$form->createView()));
        };

        return false;
    }

    /**
     * @Route("/admin/bloc/configuration", name="configurationBlocs")
     * @param Request $request
     * @return bool|Response
     */
    public function ConfigurationBlocsAction(Request $request){
        if($request->isXmlHttpRequest()){
            $action = $request->get('action');
            if($action == 'actif'){
                $typeBloc = $request->get('typeBloc');
                $type = $request->get('type');
                $actif = $request->get('actif');

                $infos = Yaml::parseFile('../src/Blocs/'.$type.'/infos.yaml');

                ($actif == 'true') ? $infos['actif'] = 'oui' : $infos['actif'] = 'non';

                $nvConfig = Yaml::dump($infos);

                file_put_contents('../src/Blocs/'.$type.'/infos.yaml', $nvConfig);

                return new Response ('ok');
            }else if($action == 'priorite'){
                $typeBloc = $request->get('typeBloc');
                $blocs = $request->get('blocs');

                foreach($blocs as $type => $priorite){
                    $infos = Yaml::parseFile('../src/Blocs/'.$type.'/infos.yaml');
                    if($infos['priorite'] != $priorite){
                        $infos['priorite'] = (int)$priorite;

                        $nvInfos = Yaml::dump($infos);

                        file_put_contents('../src/Blocs/'.$type.'/infos.yaml', $nvInfos);
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

            return $this->render('back/blocs/configuration.html.twig', array('blocs'=>$blocsContenu, 'blocsAnnexes'=>$blocsAnnexes));
        }
    }

    public function getInfosBlocs(){
        $types = scandir('../src/Blocs');
        $types = array_combine(array_values($types), array_values($types));
        unset($types["."]);
        unset($types[".."]);

        $blocs = [];
        $blocsContenu = [];
        $blocsAnnexes = [];
        foreach($types as $type){
            $infos = Yaml::parseFile('../src/Blocs/'.$type.'/infos.yaml');
            $infos['identifiant'] = $type;
            if($infos['type'] == 'contenu'){
                $blocsContenu[$type] = $infos;
            }else{
                $blocsAnnexes[$type] = $infos;
            }
        }

        $blocs['contenu'] = $blocsContenu;
        $blocs['annexe'] = $blocsAnnexes;

        return $blocs;
    }
}