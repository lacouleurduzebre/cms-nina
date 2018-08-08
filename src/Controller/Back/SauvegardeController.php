<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 08/08/2018
 * Time: 09:21
 */

namespace App\Controller\Back;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SauvegardeController extends Controller
{
    /**
     * @Route("/admin/sauvegarde", name="sauvegarde")
     * @param Request $request
     * @return bool|Response
     */
    public function sauvegardeAction(){
        //Liste de tous les dumps bdd
        $exportsBdd = scandir('sauvegardes/bdd');
        unset($exportsBdd[0]);
        unset($exportsBdd[1]);
        $exportsBdd = array_values($exportsBdd);

        $dumpsBdd = [];

        foreach($exportsBdd as $dump){
            $timestamp = str_replace(array("dump", ".sql"), '', $dump);
            $dumpsBdd[$dump] = date('d/m/Y h:i', $timestamp);
        }

        //Liste des tous les dumps médiathèque
        $exportsMediatheque = scandir('sauvegardes/mediatheque');
        unset($exportsMediatheque[0]);
        unset($exportsMediatheque[1]);
        $exportsMediatheque = array_values($exportsMediatheque);

        $dumpsMediatheque = [];

        foreach($exportsMediatheque as $dump){
            $timestamp = str_replace(array("mediatheque", ".zip"), '', $dump);
            $dumpsMediatheque[$dump] = date('d/m/Y h:i', $timestamp);
        }

        return $this->render('back/sauvegarde.html.twig', array('dumpsBdd'=>$dumpsBdd, 'dumpsMediatheque'=>$dumpsMediatheque));
    }

    /**
     * @Route("/admin/sauvegarde/bdd", name="sauvegarderBDD")
     * @param Request $request
     * @return bool|Response
     */
    public function sauvegarderBDDAction(Request $request){
        if($request->isXmlHttpRequest()){
            $env = new Dotenv();
            $env->load('../.env');
            $user = getenv('USER');
            $pswd = getenv('PASSWORD');
            $database = getenv('DATABASE');

            $timestamp = time();
            $date = date('d/m/Y h:i', $timestamp);

            exec('mysqldump -u '.$user.' -p '.$pswd.' '.$database.' > '.__DIR__.'/../../../public/sauvegardes/bdd/dump'.$timestamp.'.sql', $output);

            return new Response($timestamp.'*'.$date);
        }

        return false;
    }

    /**
     * @Route("/admin/sauvegarde/supprimerDumps", name="supprimerDumps")
     * @param Request $request
     * @return bool|Response
     */
    public function supprimerDumpsAction(Request $request){
        if($request->isXmlHttpRequest()){
            $type = $request->get('type');

            if($type == 'bdd'){
                array_map('unlink', glob("sauvegardes/bdd/*.sql"));
            }elseif($type == 'mediatheque'){
                array_map('unlink', glob("sauvegardes/mediatheque/*.zip"));
            }

            return new Response('ok');
        }

        return false;
    }

    /**
     * @Route("/admin/sauvegarde/mediatheque", name="sauvegarderMediatheque")
     * @param Request $request
     * @return bool|Response
     */
    public function sauvegardeMediathequeAction(Request $request){
        if($request->isXmlHttpRequest()){
            $timestamp = time();
            $date = date('d/m/Y h:i', $timestamp);

            $this->zip('./uploads', './sauvegardes/mediatheque/mediatheque'.$timestamp.'.zip');

            return new Response($timestamp.'*'.$date);
        }

        return false;
    }

    /**
     * Zips a folder provided to the destination zip file
     * @param  string $source      path to folder you want to zip
     * @param  string $destination destination zip file (will be created if it doesn't already exist)
     * @return boolean              true if successful
     */
    public function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if(!$zip->open($destination, \ZipArchive::CREATE)) {
            return false;
        }

        $source = str_replace('\\', DIRECTORY_SEPARATOR, realpath($source));
        $source = str_replace('/', DIRECTORY_SEPARATOR, $source);

        if(is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                $file = str_replace('/', DIRECTORY_SEPARATOR, $file);

                if ($file == '.' || $file == '..' || empty($file) || $file==DIRECTORY_SEPARATOR) continue;
                // Ignore "." and ".." folders
                if ( in_array(substr($file, strrpos($file, DIRECTORY_SEPARATOR)+1), array('.', '..')) )
                    continue;

                $file = realpath($file);
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                $file = str_replace('/', DIRECTORY_SEPARATOR, $file);

                if (is_dir($file) === true) {
                    $d = str_replace($source . DIRECTORY_SEPARATOR, '', $file );
                    if(empty($d)) continue;
                    print "Making DIRECTORY {$d}<Br>";
                    $zip->addEmptyDir($d);
                } elseif (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR, '', $file), file_get_contents($file));
                } else {
                    // do nothing
                }
            }
        } elseif (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
}