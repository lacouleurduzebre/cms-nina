<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 07/08/2018
 * Time: 15:59
 */

namespace App\Controller\Back;


use App\Controller\SEOController;
use App\Entity\Configuration;
use App\Form\Type\ParametresThemes\ParametrageThemeType;
use App\Service\Droits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ThemeController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class ThemeController extends AbstractController
{
    /**
     * @Route("/theme", name="theme")
     */
    public function themeAction(Droits $droits){
        if(!$droits->checkDroit('themes')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

        $themes = $this->listeThemes();

        $entityConfig = ['name' => 'Theme'];

        return $this->render('back/themes/themes.html.twig', array('themesInstalles' => $themes['installes'], 'themesDisponibles' => $themes['disponibles'], '_entity_config' => $entityConfig));
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
            $this->changementTheme($theme, $filesystem);

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
            $theme = $request->get('nom');

            if(ThemeController::installationTheme($theme, $lien, $filesystem)){
                return new Response('ok');
            }
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
    public function parametrerThemeAction(Droits $droits, Request $request, $nom){
        if(!$droits->checkDroit('themes')){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette page");
        }

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

            $anciennesValeurs = [];
            foreach($champs as $champ => $valeur){
                if($parametres && key_exists($champ, $parametres)){//Paramètre modifié par l'utilisateur
                    $champs[$champ] = $anciennesValeurs[$champ] = $parametres[$champ];
                }else{//Paramètre par défaut
                    $champs[$champ] = $anciennesValeurs[$champ] = $parametres[$champ] = $valeur;
                }

                //Remplacement des variables par leur valeur
                if(!is_array($anciennesValeurs[$champ]) && substr($anciennesValeurs[$champ], 0, 1) == '$'){
                    $champs[$champ] = $anciennesValeurs[str_replace('$', '', $anciennesValeurs[$champ])];
                }
            }

            //Formulaire
            $form = $this->createForm(ParametrageThemeType::class, $parametres);

            //Enregistrement des paramètres
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();

                $nomFichierVariablesScss = '../themes/'.$nom.'/assets/css/_config/_parametres.scss';
                fopen($nomFichierVariablesScss, "w");

                foreach($data as $champ=>$valeur){
                    //Modif fichier scss
                    if($champ == 'polices'){//Si police
                        foreach($valeur as $police){
                            file_put_contents($nomFichierVariablesScss, file_get_contents($nomFichierVariablesScss).PHP_EOL."@import url('https://fonts.googleapis.com/css2?family=".$police."');");
                        }
                    }elseif($champ == 'stylesBlocs'){
                        foreach($valeur as $style){
                            $class = '.'.SEOController::slugify($style['nom'], '_').'{';

                            if($style['opaciteFond'] && $style['couleurFond']){
                                $hex = $style['couleurFond'];
                                list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
                                $class .= PHP_EOL.'background-color: rgb('.$r.','.$g.','.$b.','.$style['opaciteFond'].');';
                            }elseif($style['couleurFond']){
                                $class .= PHP_EOL.'background-color: '.$style['couleurFond'].';';
                            }

                            if($style['couleur']){
                                $class .= PHP_EOL.'color: '.$style['couleur'].';';
                            }

                            $class .= PHP_EOL.'}';

                            file_put_contents($nomFichierVariablesScss, file_get_contents($nomFichierVariablesScss).PHP_EOL.$class);
                        }
                    }else{
                        if(strpos($champ, 'police') > 0){
                            $valeur = '"'.$valeur.'"';
                        }

                        file_put_contents($nomFichierVariablesScss, file_get_contents($nomFichierVariablesScss).PHP_EOL.'$'.$champ.': '.$valeur.';');
                    }

                    //Modif config.yaml
                    $parametres[$champ] = $valeur;
                }

                //Compilation SCSS / enregistrement fichier css
                $nomFichierCss = '../themes/'.$nom.'/assets/css/knacss.css';
                exec('pwd', $pwd);
                exec($pwd[0].'/../vendor/scssphp/scssphp/bin/pscss '.$pwd[0].'/../themes/'.$nom.'/assets/css/knacss.scss', $css);
                file_put_contents($nomFichierCss, $css);

                //Enregistrement config.yaml
                $nvFichier = Yaml::dump($parametres);
                file_put_contents($nomFichierParametres, $nvFichier);

                $this->addFlash('enregistrement', 'Les paramètres ont été enregistrés');
            }
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

    public static function listeThemes($separes = true){
        $nomThemes = scandir('../themes');
        unset($nomThemes[0]);
        unset($nomThemes[1]);
        $nomThemes = array_values($nomThemes);

        $themesInstalles = [];
        foreach($nomThemes as $nomTheme){
            $config = Yaml::parseFile('../themes/'.$nomTheme.'/config.yaml');
            $infos = $config['infos'];
            $themesInstalles[$nomTheme] = $infos;
            $themesInstalles[$nomTheme]['installe'] = 1;

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
        $themesDisponibles = [];
        $themesExternes = Yaml::parse(file_get_contents('https://www.cms-nina.fr/themes-nina/themes-nina.yml'));
        foreach($themesExternes as $nomTheme => $theme){
            if(!array_key_exists($nomTheme, $themesInstalles)){
                $themesDisponibles[$nomTheme] = $theme;
                $themesDisponibles[$nomTheme]['installe'] = 0;
            }else{
                $themesInstalles[$nomTheme]['lien'] = $theme['lien'];
            }
        }
        //Fin thèmes externes

        if($separes){
            return ['installes' => $themesInstalles, 'disponibles' => $themesDisponibles];
        }else{
            return ['themes' => array_merge($themesInstalles, $themesDisponibles)];
        }
    }

    public static function installationTheme($theme, $lien, Filesystem $filesystem){
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
            $dossierTheme = '../themes/'.$theme;
            $filesystem->rename('../themes/'.$nomDossier, $dossierTheme, true);

            //Création du dossier "translations" s'il n'existe pas
            if(!file_exists($dossierTheme.'/translations')){
                mkdir($dossierTheme.'/translations');
            }

            //Création du dossier "templates" s'il n'existe pas
            if(!file_exists($dossierTheme.'/templates')){
                mkdir($dossierTheme.'/templates');
            }

            return true;
        } else {
            return false;
        }
    }

    public static function changementTheme($theme, Filesystem $filesystem){
        //Modification de la configuration Twig
        $fichier = Yaml::parseFile('../config/services.yaml');

        $fichier['parameters']['theme'] = $theme;

        $nvFichier = Yaml::dump($fichier);

        file_put_contents('../config/services.yaml', $nvFichier);

        $linkFileT = './../themes/'.$theme;

        //Téléchargement du thème s'il n'est pas installé
        if(!file_exists($linkFileT)){
            $themesExternes = Yaml::parse(file_get_contents('https://www.cms-nina.fr/themes-nina/themes-nina.yml'));
            $lien = $themesExternes[$theme]['lien'];

            ThemeController::installationTheme($theme, $lien, $filesystem);
        }

            //Suppression lien précédent
        if(file_exists('theme')) {
            if(is_link('theme')) {
                if(strpos(php_uname('s'), 'Win') !== false){
                    rmdir('theme');
                }else{
                    unlink('theme');
                }
            }
        }

            //Création nouveau lien
        if(strpos(php_uname('s'), 'Win') !== false){
            $filesystem->symlink($linkFileT.'/assets', 'theme');
        }else{
            exec('ln -s ./../themes/'.$theme.'/assets theme');
        }
        //Fin Symlink
    }
}