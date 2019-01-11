<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/08/2018
 * Time: 15:59
 */

namespace App\Controller\Back;


use App\Entity\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class ThemeController extends Controller
{
    /**
     * @Route("/admin/theme", name="theme")
     */
    public function themeAction(){
        $nomThemes = scandir('../themes');
        unset($nomThemes[0]);
        unset($nomThemes[1]);
        $nomThemes = array_values($nomThemes);

        $themes = [];
        foreach($nomThemes as $nomTheme){
            $config = Yaml::parseFile('../themes/'.$nomTheme.'/config.yaml');
            $infos = $config['infos'];
            $themes[$nomTheme] = $infos;

            //Miniature
            if(!file_exists(getcwd().'/themes_thumbs')){
                mkdir(getcwd().'/themes_thumbs');
            }

            $miniature = getcwd().'/../themes/'.$nomTheme.'/thumb.jpg';
            $lien = getcwd().'/themes_thumbs/'.$nomTheme.'.jpg';
            if(!file_exists($lien)){
                if(file_exists($miniature)){
                    copy($miniature, $lien);
                }
            }
        }

        return $this->render('back/theme.html.twig', array('themes' => $themes));
    }

    /**
     * @Route("/theme/modifier", name="modifierTheme")
     */
    public function modifierThemeAction(Request $request){
        if($request->isXmlHttpRequest()){
            $theme = $request->get('theme');

            //Enregistrement du thème en bdd
            $em=$this->getDoctrine()->getManager();
            $repositoryConfig = $this->getDoctrine()->getRepository(Configuration::class);
            $config = $repositoryConfig->find(1);

            $config->setTheme($theme);

            $em->persist($config);
            $em->flush();

            //Modification de la configuration Twig
            $fichier = Yaml::parseFile('../config/services.yaml');

            $fichier['parameters']['theme'] = $theme;

            $nvFichier = Yaml::dump($fichier);

            file_put_contents('../config/services.yaml', $nvFichier);

            //Symlink
                //Suppression lien précédent
            $linkfile = getcwd().'/theme';
            if(file_exists($linkfile)) {
                if(is_link($linkfile)) {
                    rmdir($linkfile);
                }
            }

                //Création nouveau lien
            symlink(getcwd().'/../themes/'.$theme.'/assets', $linkfile);

            //Fin Symlink

            return new Response('ok');
        };

        return false;
    }
}