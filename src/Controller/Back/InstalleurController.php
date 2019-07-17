<?php
/**
 * Created by PhpStorm.
 * User: nadege
 * Date: 2019-07-17
 * Time: 09:40
 */

namespace App\Controller\Back;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function installeur($etape){
        switch($etape){
            case 1:
                $form = $this->createFormBuilder()
                    ->add('host', TextType::class, ['label' => 'Serveur'])
                    ->add('database', TextType::class, ['label' => 'Nom de la base de données'])
                    ->add('userdb', TextType::class, ['label' => 'Utilisateur'])
                    ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
                    ->add('prefixe', TextType::class, ['label' => 'Préfixe'])
                    ->add('Étape suivante', SubmitType::class)
                    ->getForm();

                return $this->render('installeur/1_configBDD.html.twig', ['form' => $form->createView()]);
            case 2:

        }
    }

    /**
     * @Route("/installeur/0", name="testConnexion")
     * @param Request $request
     * @return @return bool|Response
     */
    public function testConnexion(Request $request){
        if($request->isXmlHttpRequest()){
            $data = $request->get('form');

            $path = '../.env';
            if (file_exists($path)) {
                foreach ($data as $donnee) {
                    preg_match_all("/\\[(.*?)\\]/", $donnee['name'], $matches);
                    $cle = $matches[1][0];

                    if ($cle != '_token') {
                        file_put_contents($path, str_replace(
                            strtoupper($cle) . '=' . $_ENV[strtoupper($cle)], strtoupper($cle) . '=' . $donnee['value'], file_get_contents($path)
                        ));
                    }
                }
            }

            $connexion = $this->getDoctrine()->getConnection()->connect() ? 'ok' : 'echec';

            return new Response($connexion);
        };

        return false;
    }
}