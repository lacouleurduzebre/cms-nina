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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

class FormulaireController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route(path: '/envoiMail', name: 'envoiMail')]
    public function envoiMailAction(Request $request, MailerInterface $mailer){
        if($request->isXmlHttpRequest()){
            $donnees = $request->request->all('donnees');
            $idBloc = $request->request->get('idBloc');

            //Antispam
            $mielValeur = array_values(array_filter($donnees, function($ar) {
                return ($ar['name'] == 'miel_valeur');
            }));
            $mielRempli = array_values(array_filter($donnees, function($ar) {
                return ($ar['name'] == 'miel_rempli');
            }));
            $mielVide = array_values(array_filter($donnees, function($ar) {
                return ($ar['name'] == 'miel_vide');
            }));

            if($mielValeur[0]['value'] === $mielRempli[0]['value'] && $mielVide[0]['value'] == ''){
                $bloc = $this->doctrine->getRepository(Bloc::class)->find($idBloc);
                $destinataires = $bloc->getContenu()['destinataires'];
                $objet = $bloc->getContenu()['objet'];

                $config = $this->doctrine->getRepository(Configuration::class)->find(1);
                $expediteur = $config->getEmailContact();

                //Suppression des champs antispam
                $donnees = array_filter($donnees, function($ar) {
                    return ($ar['name'] != 'miel_valeur' && $ar['name'] != 'miel_rempli' && $ar['name'] != 'miel_vide');
                });

                //Préparation mail
                $mail = (new Email())
                    ->subject($objet)
                    ->from($expediteur)
                    ->to(...array_values(array_filter($destinataires)))
                    ->html($this->renderView('Blocs/Formulaire/Mail.html.twig', array('donnees' => $donnees)));

                $mailer->send($mail);

                return new Response($bloc->getContenu()['messageConfirmation']);
            }
            return new Response("Le formulaire a été soumis trop rapidement. Attendez 3 secondes avant de soumettre à nouveau le formulaire.");
        }
        return false;
    }
}