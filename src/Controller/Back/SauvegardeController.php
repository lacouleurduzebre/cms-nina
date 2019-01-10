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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
        $exportsBdd = scandir('../sauvegardes/bdd');
        $exportsBdd = array_combine(array_values($exportsBdd), array_values($exportsBdd));
        unset($exportsBdd["."]);
        unset($exportsBdd[".."]);
        unset($exportsBdd[".DS_Store"]);

        $dumpsBdd = [];

        foreach($exportsBdd as $dump){
            $timestamp = str_replace(array("dump", ".zip"), '', $dump);
            $dumpsBdd[$dump] = date('d/m/Y h:i', $timestamp);
        }

        //Liste des tous les dumps médiathèque
        $exportsMediatheque = scandir('../sauvegardes/mediatheque');
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
            $timestamp = time();
            $date = date('d/m/Y h:i', $timestamp);

            $env = new Dotenv();
            $env->load('../.env');
            $mysqlUserName      = getenv('USER');
            $mysqlPassword      = getenv('PASSWORD');
            $mysqlHostName      = getenv('HOST');
            $DbName             = getenv('DATABASE');
            $backup_name        = "dump".$timestamp.".sql";

            $this->exportBDD($mysqlHostName,$mysqlUserName,$mysqlPassword,$DbName,  $tables=false, $backup_name );

            $this->zip('./../sauvegardes/bdd/dump'.$timestamp.'.sql', './../sauvegardes/bdd/dump'.$timestamp.'.zip');

            unlink('./../sauvegardes/bdd/dump'.$timestamp.'.sql');

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
            $fichier = $request->get('fichier');

            if($type == 'bdd'){
                array_map('unlink', glob("../sauvegardes/bdd/".$fichier));
            }elseif($type == 'mediatheque'){
                array_map('unlink', glob("../sauvegardes/mediatheque/".$fichier));
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

            $this->zip('./uploads', './../sauvegardes/mediatheque/mediatheque'.$timestamp.'.zip');

            return new Response($timestamp.'*'.$date);
        }

        return false;
    }

    /**
     * @Route("/admin/sauvegarde/telechargerDump", name="telechargerDump")
     * @param Request $request
     * @return bool|Response
     */
    public function telechargerMediathequeAction(Request $request){
        $type = $request->get('type');
        $fichier = $request->get('fichier');

        $response = new BinaryFileResponse('../sauvegardes/'.$type.'/'.$fichier);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fichier
        );

        return $response;
    }

    public function exportBDD($host,$user,$pass,$name, $tables=false, $backup_name=false)
    {
        $mysqli = new \mysqli($host,$user,$pass,$name);
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");

        $queryTables    = $mysqli->query('SHOW TABLES');
        while($row = $queryTables->fetch_row())
        {
            $target_tables[] = $row[0];
        }
        foreach($target_tables as $table)
        {
            $result         =   $mysqli->query('SELECT * FROM '.$table);
            $fields_amount  =   $result->field_count;
            $rows_num=$mysqli->affected_rows;
            $res            =   $mysqli->query('SHOW CREATE TABLE '.$table);
            $TableMLine     =   $res->fetch_row();
            $content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0)
            {
                while($row = $result->fetch_row())
                { //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 )
                    {
                        $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)
                    {
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
                        if (isset($row[$j]))
                        {
                            $content .= '"'.$row[$j].'"' ;
                        }
                        else
                        {
                            $content .= '""';
                        }
                        if ($j<($fields_amount-1))
                        {
                            $content.= ',';
                        }
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num)
                    {
                        $content .= ";";
                    }
                    else
                    {
                        $content .= ",";
                    }
                    $st_counter=$st_counter+1;
                }
            } $content .="\n\n\n";
        }
        $backup_name = $backup_name ? $backup_name : $name.".sql";

        file_put_contents('./../sauvegardes/bdd/'.$backup_name, $content);
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