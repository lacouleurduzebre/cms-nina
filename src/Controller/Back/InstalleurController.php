<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-07-17
 * Time: 09:40
 */

namespace App\Controller\Back;


use App\Blocs\Image\ImageType;
use App\Entity\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstalleurController extends Controller
{
    /**
     * @Route("/installeur/{etape}", name="installeur", requirements={
     *     "etape"="^[1-9]{1,1}$"
     * })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installeur($etape, Request $request){
        switch($etape){
            case 1: //Configuration de la BDD
                $form = $this->createFormBuilder()
                    ->add('host', TextType::class, ['label' => 'Serveur'])
                    ->add('database', TextType::class, ['label' => 'Nom de la base de données'])
                    ->add('userdb', TextType::class, ['label' => 'Utilisateur'])
                    ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
                    ->add('prefixe', TextType::class, ['label' => 'Préfixe'])
                    ->add('Étape suivante', SubmitType::class)
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();

                    if($this->testConnexion($request, $data) == 'ok'){

                        exec('which php', $php);
                        exec($php[0].' ../bin/console doctrine:migrations:diff --filter-expression=/^'.$_ENV['PREFIXE'].'_/ -nq; '.$php[0].' ../bin/console doctrine:migrations:migrate -nq');

                        return $this->redirectToRoute('installeur', ['etape' => 2]);
                    }
                }

                return $this->render('installeur/1_configBDD.html.twig', ['form' => $form->createView()]);
            case 2: //Configuration du site
                try {
                    $this->getDoctrine()->getConnection()->connect();
                } catch (\Exception $e) {
                    return $this->redirectToRoute('installeur', ['etape' => 1]);
                }

                $repoConfig = $this->getDoctrine()->getRepository(Configuration::class);
                if($repoConfig->find(1)){
                    return $this->redirectToRoute('installeur', ['etape' => 3]);
                }

                $config = new Configuration();

                $form = $this->createFormBuilder($config)
                    ->add('nom', TextType::class, ['label' => 'Nom du site'])
                    ->add('editeur', TextType::class, ['label' => 'Éditeur du site'])
                    ->add('emailContact', EmailType::class, ['label' => 'E-mail de contact'])
                    ->add('emailMaintenance', EmailType::class, ['label' => 'E-mail de maintenance'])
                    ->add('logo', FileType::class, ['label' => 'Logo'])
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $config = $form->getData();

                    $em = $this->getDoctrine()->getManager();

                    $config->setMaintenance(1);
                    $em->persist($config);
                    $em->flush();

                    return $this->redirectToRoute('installeur', ['etape' => 4]);
                }

                return $this->render('installeur/2_configSite.html.twig', ['form' => $form->createView()]);
        }
    }

    /**
     * @Route("/installeur/0", name="testConnexion")
     * @param Request $request
     * @return @return bool|Response
     */
    public function testConnexion(Request $request, $data = null){
        $path = '../.env';
        if (file_exists($path)) {
            if ($request->isXmlHttpRequest()) {
                $data = $request->get('form');

                foreach ($data as $donnee) {
                    preg_match_all("/\\[(.*?)\\]/", $donnee['name'], $matches);
                    $cle = $matches[1][0];

                    if ($cle != '_token' && $donnee['value'] != '') {
                        file_put_contents($path, str_replace(
                            strtoupper($cle) . '=' . $_ENV[strtoupper($cle)], strtoupper($cle) . '=' . $donnee['value'], file_get_contents($path)
                        ));
                    }
                }
            }else{
                foreach ($data as $cle => $donnee) {
                    if($donnee != ''){
                        file_put_contents($path, str_replace(
                            strtoupper($cle) . '=' . $_ENV[strtoupper($cle)], strtoupper($cle) . '=' . $donnee, file_get_contents($path)
                        ));
                    }
                }
            }


            try {
                $this->getDoctrine()->getConnection()->connect();
            } catch (\Exception $e) {
                if ($request->isXmlHttpRequest()) {
                    return new Response('echec');
                }else{
                    return 'echec';
                }
            }

            if ($request->isXmlHttpRequest()) {
                return new Response('ok');
            }else{
                return 'ok';
            }
        }
    }
}