<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2018
 * Time: 11:24
 */

namespace App\Blocs\Formulaire;


use App\Entity\Bloc;
use App\Entity\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormulaireController extends Controller
{
    /**
     * @Route("/admin/envoiMail", name="envoiMail")
     */
    public function envoiMailAction(Request $request, \Swift_Mailer $mailer){
        if($request->isXmlHttpRequest()){
            $donnees = $request->get('donnees');
            $idBloc = $request->get('idBloc');

            $bloc = $this->getDoctrine()->getRepository(Bloc::class)->find($idBloc);
            $destinataires = $bloc->getContenu()['destinataires'];
            $objet = $bloc->getContenu()['objet'];

            $config = $this->getDoctrine()->getRepository(Configuration::class)->find(1);
            $expediteur = $config->getEmailContact();

            //Préparation mail
            $mail = new \Swift_Message($objet);
            $mail->setFrom($expediteur)
                ->setTo($destinataires)
                ->setBody($this->renderView('Blocs/Formulaire/Mail.html.twig', array('donnees' => $donnees)), 'text/html');

            $mailer->send($mail);

            return new Response($bloc->getContenu()['messageConfirmation']);
        }
        return false;
    }
}