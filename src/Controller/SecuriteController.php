<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Utilisateur;
use App\Form\Type\MotDePasseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Connexion, profil et mot de passe (remplace les contrôleurs de FOSUserBundle, mêmes URLs)
 */
class SecuriteController extends AbstractController
{
    //Délai avant une nouvelle demande de réinitialisation du mot de passe
    private const DELAI_NOUVELLE_DEMANDE = 7200;
    //Durée de validité du lien de réinitialisation
    private const DUREE_JETON = 86400;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/login', name: 'connexion', methods: ['GET', 'POST'])]
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('securite/connexion.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    //Intercepté par le pare-feu (form_login)
    #[Route(path: '/login_check', name: 'connexion_verification', methods: ['POST'])]
    public function verification(): never
    {
        throw new \LogicException('Route interceptée par le pare-feu.');
    }

    //Intercepté par le pare-feu (logout)
    #[Route(path: '/logout', name: 'deconnexion', methods: ['GET', 'POST'])]
    public function deconnexion(): never
    {
        throw new \LogicException('Route interceptée par le pare-feu.');
    }

    #[Route(path: '/profile/', name: 'profil', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function profil(): Response
    {
        return $this->render('securite/profil.html.twig', ['user' => $this->getUser()]);
    }

    #[Route(path: '/profile/change-password', name: 'profil_mot_de_passe', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function changementMotDePasse(Request $request): Response
    {
        $utilisateur = $this->getUser();

        $form = $this->createForm(MotDePasseType::class, $utilisateur, ['mot_de_passe_actuel' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Le mot de passe a été modifié.');

            return $this->redirectToRoute('profil');
        }

        return $this->render('securite/changementMotDePasse.html.twig', ['form' => $form]);
    }

    #[Route(path: '/resetting/request', name: 'mot_de_passe_oublie', methods: ['GET'])]
    public function motDePasseOublie(): Response
    {
        return $this->render('securite/motDePasseOublie.html.twig');
    }

    #[Route(path: '/resetting/send-email', name: 'mot_de_passe_oublie_envoi', methods: ['POST'])]
    public function envoiEmail(Request $request, MailerInterface $mailer): Response
    {
        $identifiant = (string) $request->request->get('username');
        $utilisateur = $identifiant ? $this->em->getRepository(Utilisateur::class)->loadUserByIdentifier($identifiant) : null;

        if ($utilisateur && !$utilisateur->isPasswordRequestNonExpired(self::DELAI_NOUVELLE_DEMANDE)) {
            if (null === $utilisateur->getConfirmationToken()) {
                $utilisateur->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
            }

            $config = $this->em->getRepository(Configuration::class)->find(1);
            $expediteur = $config->getEmailMaintenance();

            $mail = (new Email())
                ->subject('Réinitialisation de votre mot de passe')
                ->from($expediteur)
                ->to($utilisateur->getEmail())
                ->text($this->renderView('securite/mails/reinitialisation.txt.twig', [
                    'user' => $utilisateur,
                    'lien' => $this->generateUrl('mot_de_passe_reinitialisation', ['token' => $utilisateur->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                ]));

            $mailer->send($mail);

            $utilisateur->setPasswordRequestedAt(new \DateTime());
            $this->em->flush();
        }

        return $this->redirectToRoute('mot_de_passe_oublie_email_envoye', ['username' => $identifiant]);
    }

    #[Route(path: '/resetting/check-email', name: 'mot_de_passe_oublie_email_envoye', methods: ['GET'])]
    public function emailEnvoye(Request $request): Response
    {
        if (!$request->query->get('username')) {
            return $this->redirectToRoute('mot_de_passe_oublie');
        }

        return $this->render('securite/emailEnvoye.html.twig', [
            'dureeAvantNouvelleDemande' => ceil(self::DELAI_NOUVELLE_DEMANDE / 3600),
        ]);
    }

    #[Route(path: '/resetting/reset/{token}', name: 'mot_de_passe_reinitialisation', methods: ['GET', 'POST'])]
    public function reinitialisation(Request $request, Security $security, string $token): Response
    {
        $utilisateur = $this->em->getRepository(Utilisateur::class)->findOneBy(['confirmationToken' => $token]);

        if (!$utilisateur) {
            return $this->redirectToRoute('connexion');
        }

        if (!$utilisateur->isPasswordRequestNonExpired(self::DUREE_JETON)) {
            return $this->redirectToRoute('mot_de_passe_oublie');
        }

        $form = $this->createForm(MotDePasseType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setConfirmationToken(null)
                ->setPasswordRequestedAt(null)
                ->setEnabled(true);
            $this->em->flush();

            $this->addFlash('success', 'Le mot de passe a été réinitialisé avec succès.');

            //Connexion directe, comme FOSUserBundle
            $security->login($utilisateur, 'form_login', 'principal');

            return $this->redirectToRoute('profil');
        }

        return $this->render('securite/reinitialisation.html.twig', ['form' => $form, 'token' => $token]);
    }
}
