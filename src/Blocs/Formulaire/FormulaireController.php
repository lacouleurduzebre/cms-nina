<?php
/**
 * Created by PhpStorm.
 * User: Chipolata
 * Date: 12/09/2018
 * Time: 11:24
 */

namespace App\Blocs\Formulaire;


use App\Entity\Bloc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

            //Préparation mail
            /*$mail = new \Swift_Message('Contact');
            $mail->setFrom('blabla')//Expéditeur ??
                ->setTo($destinataires)
                ->setBody($this->render('Blocs/Formulaire/Mail.html.twig', array('donnees' => $donnees)));

            $mailer->send($mail);*/

            return $this->render('Blocs/Formulaire/Mail.html.twig', array('donnees' => $donnees));
        }
        return false;
    }
}