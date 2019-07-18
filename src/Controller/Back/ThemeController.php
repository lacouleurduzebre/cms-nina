<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/08/2018
 * Time: 15:59
 */

namespace App\Controller\Back;


use App\Entity\Configuration;
use App\Form\Type\ImageSimpleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ThemeController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class ThemeController extends Controller
{
    /**
     * @Route("/theme", name="theme")
     */
    public function themeAction(){
        $themes = $this->listeThemes();

        $entityConfig = ['name' => 'Theme'];

        return $this->render('back/themes/theme.html.twig', array('themes' => $themes, '_entity_config' => $entityConfig));
    }

    /**
     * @Route("/theme/changer", name="changerTheme")
     */
    public function changerThemeAction(Request $request, Filesystem $filesystem){
        if($request->isXmlHttpRequest()){
            $theme = $request->get('theme');

            //Enregistrement du thème en bdd
            $em=$this->getDoctrine()->getManager();
            $repositoryConfig = $this->getDoctrine()->getRepository(Configuration::class);
            $config = $repositoryConfig->find(1);

            $config->setTheme($theme);

            $em->persist($config);
            $em->flush();

            //Config Twig et symlink
            $linkfileP = $this->getParameter('kernel.project_dir').'/public/theme';
            $linkfileT = $this->getParameter('kernel.project_dir').'/themes/';
            $this->changementTheme($theme, $linkfileP, $linkfileT, $filesystem);

            return new Response('ok');
        };

        return false;
    }

    /**
     * @Route("/theme/installer", name="installerTheme")
     */
    public function installerThemeAction(Request $request, Filesystem $filesystem){
        if($request->isXmlHttpRequest()){
            $lien = $request->get('lien');
            $nom = $request->get('nom');

            $tmp = '../themes/theme.zip';

            copy($lien, $tmp);

            $zip = new \ZipArchive();
            $fichier = $zip->open($tmp);
            if ($fichier) {
                //Nom du dossier contenu dans le zip
                $nomDossier = substr($zip->getNameIndex(0), 0, -1);

                //Extraction
                $zip->extractTo('../themes/');
                $zip->close();

                //Suppression zip
                unlink($tmp);

                //Renommage dossier
                $dossierTheme = '../themes/'.$nom;
                $filesystem->rename('../themes/'.$nomDossier, $dossierTheme, true);

                //Création du dossier "translations" s'il n'existe pas
                if(!file_exists($dossierTheme.'/translations')){
                    mkdir($dossierTheme.'/translations');
                }
            } else {
                return false;
            }

            return new Response('ok');
        };

        return false;
    }

    /**
     * @Route("/theme/desinstaller", name="desinstallerTheme")
     */
    public function desinstallerThemeAction(Request $request, Filesystem $filesystem){
        if($request->isXmlHttpRequest()){
            $nom = $request->get('nom');

            if(strpos(php_uname('s'), 'Win') !== false){
                exec('echo %cd%', $pwd);
                exec('del /s /q  '.$pwd[0].'/../themes/'.$nom);
            }else{
                exec('pwd', $pwd);
                exec('rm -rf '.$pwd[0].'/../themes/'.$nom);
            }

            return new Response('ok');
        };

        return false;
    }

    /**
     * @Route("/theme/parametrer/{nom}", name="parametrerTheme")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function parametrerThemeAction(Request $request, $nom){
        //Champs
        $fichierDefaut = Yaml::parseFile('../themes/'.$nom.'/config.yaml');

        if(key_exists('champs', $fichierDefaut)){
            $champs = $fichierDefaut['champs'];

            //Valeurs
            $nomFichierParametres = '../themes/'.$nom.'/parametres.yaml';
            if(!file_exists($nomFichierParametres)){
                $fichiersParametres = fopen($nomFichierParametres, "w");
                fclose($fichiersParametres);
            }
            $parametres = Yaml::parseFile($nomFichierParametres);

            //Formulaire
            $form = $this->createForm(FormType::class);

            foreach($champs as $champ => $infos){
                if($parametres && key_exists($nom, $parametres)){//Paramètre modifié par l'utilisateur
                    $data = $parametres[$nom];
                }else{//Paramètre par défaut
                    $data = $infos['defaut'];
                }

                if($infos['type'] == 'image'){
                    $form->add($champ, ImageSimpleType::class, array(
                        'label' => $infos['label']
                    ));
                }else{
                    $form->add($champ, 'Symfony\Component\Form\Extension\Core\Type\\'.ucfirst($infos['type']).'Type', array(
                        'label' => $infos['label']
                    ));
                }

                $form->get($champ)->setData($data);
            }

            $form->add('Envoyer', SubmitType::class);
        }else{//Pas de paramètres
            $champs = null;
        }

        //Template
        return $this->render('back/themes/parametres.html.twig', array('nom' => $nom, 'champs' => $champs, 'form' => isset($form) ? $form->createView() : null ));
    }

    /**
     * @Route("/theme/maj", name="majTheme")
     */
    public function majThemeAction(Request $request){
        if($request->isXmlHttpRequest()){

        };

        return false;
    }

    public static function listeThemes(){
        $nomThemes = scandir('../themes');
        unset($nomThemes[0]);
        unset($nomThemes[1]);
        $nomThemes = array_values($nomThemes);

        $themes = [];
        foreach($nomThemes as $nomTheme){
            $config = Yaml::parseFile('../themes/'.$nomTheme.'/config.yaml');
            $infos = $config['infos'];
            $themes[$nomTheme] = $infos;
            $themes[$nomTheme]['installe'] = 1;

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

        //Thèmes externes
        $themesExternes = Yaml::parse(file_get_contents('https://www.cms-nina.fr/themes-nina/themes-nina.yml'));
        foreach($themesExternes as $nomTheme => $theme){
            if(!array_key_exists($nomTheme, $themes)){
                $themes[$nomTheme] = $theme;
                $themes[$nomTheme]['installe'] = 0;
            }else{
                $themes[$nomTheme]['lien'] = $theme['lien'];
            }
        }
        //Fin thèmes externes

        return $themes;
    }

    public static function changementTheme($theme, $linkfileP, $linkfileT, Filesystem $filesystem){
        //Modification de la configuration Twig
        $fichier = Yaml::parseFile('../config/services.yaml');

        $fichier['parameters']['theme'] = $theme;

        $nvFichier = Yaml::dump($fichier);

        file_put_contents('../config/services.yaml', $nvFichier);

        //Symlink
            //Suppression lien précédent
        if(file_exists($linkfileP)) {
            if(is_link($linkfileP)) {
                if(strpos(php_uname('s'), 'Win') !== false){
                    rmdir($linkfileP);
                }else{
                    unlink($linkfileP);
                }
            }
        }

            //Création nouveau lien
        if(strpos(php_uname('s'), 'Win') !== false){
            $filesystem->symlink($linkfileT.$theme.'/assets', $linkfileP);
        }else{
            exec('ln -s ../themes/'.$theme.'/assets '.$linkfileP);
        }
        //Fin Symlink
    }
}