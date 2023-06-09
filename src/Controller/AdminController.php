<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CsvUploadType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Repository\CampusRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * @Route("/admin", name="app_admin_")
 */
class AdminController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private ResetPasswordHelperInterface $resetPasswordHelper;

    private MailerInterface $mailer;

    /**
     * @param EmailVerifier $emailVerifier
     */
    public function __construct(MailerInterface $mailer, ResetPasswordHelperInterface $resetPasswordHelper, EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->mailer= $mailer;

    }

    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @throws TransportExceptionInterface
     * @throws \Exception
     * @throws ResetPasswordExceptionInterface
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new Participant();
        $user->setIsVerified(false);
        $user->setActif(true);
        $isGrantedUser = $this->isGranted('ROLE_ADMIN');

        $form = $this->createForm(RegistrationFormType::class, $user,
            ["isGrantedUser" => $isGrantedUser]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles($form->get('roles')->getData());
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('motdepasse')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription crée !');


            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_admin_app_verify_email', $user,
                   (new TemplatedEmail())
                       ->from(new Address('contact@goout.com', 'Go Out Mail'))
                       ->to($user->getEmail())
                       ->subject('Please Confirm your Email')
                       ->htmlTemplate('registration/confirmation_email.html.twig')
               );

            // envoyer un mail pour changer le mot de passe par le user
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);

            $email = (new TemplatedEmail())
                ->from('contact@goout.com')
                ->to($user->getEmail())
                ->subject('Modification du mot de passe')
                ->htmlTemplate('reset_password/email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                ]);

            $this->mailer->send($email);

            return $this->redirectToRoute('main_home');
      }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_admin_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_admin_register');
    }

    /**
     * @Route("/users", name="liste_users")
     */
    public function listeparticipants(ParticipantRepository $participantRepository): Response
    {
        $users = $participantRepository->findAll();
        return $this->render('user/listeparticipants.html.twig', ['users' => $users
        ]);
    }
    /**
     * @Route("/user/desactiver/{id}", name="desactiver_user", requirements={"id"="\d+"})
     */
    public function desactiverUser(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $participantRepository->find($id);
        $user->setActif(false);
        $entityManager->flush($user);
        $this->addFlash('success', "L'utilisateur est maintenant inactif");

        return $this->redirectToRoute('app_admin_liste_users');



    }

    /**
     * @Route("/user/activer/{id}", name="activer_user", requirements={"id"="\d+"})
     */
    public function activerUser(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $participantRepository->find($id);
        $user->setActif(true);
        $entityManager->flush($user);
        $this->addFlash('success', "L'utilisateur est maintenant actif");

        return $this->redirectToRoute('app_admin_liste_users');

    }

    /**
     * @Route("/upload-csv", name="upload_csv")
     */
    public function uploadCsv(Request $request, \Doctrine\ORM\EntityManagerInterface $entityManager, CampusRepository $campusRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        $form = $this->createForm(CsvUploadType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('csvFile')->getData();
            // EXTRAIRE LES DONNEES DU CSV
            $csvData = file_get_contents($file->getPathname());
            $lignes = array_map('str_getcsv', explode("\n", $csvData));
            $header = array_shift($lignes); // Retire la première ligne de $lignes et la retourne pour stocker dans $header on pourra ainsi
            //                                        gèrer des variations dans l'ordre des colonnes
            // EXTRAIRE LES USERS DES DONNEES
            $users = [];
            foreach ($lignes as $ligne) {
                $userData = array_combine($header, $ligne); // associer les headers à la ligne

                $user = new Participant();
                $user->setNom($userData['nom']);
                $user->setPrenom($userData['prenom']);
                $user->setPseudo($userData['pseudo']);
                $user->setEmail($userData['email']);
                $user->setPassword($userPasswordHasher->hashPassword($user, $userData['password']));
                $user->setTelephone($userData['telephone']);
                $user->setCampus($campusRepository->findOneByNom($userData['campus']));
                $user->setRoles([$userData['role']]);
                $user->setActif(1);

                $users[] = $user;
            }
            // STOCKER LES USERS DANS LA BDD
            foreach ($users as $user) {
                $entityManager->persist($user);
            }
            $entityManager->flush();
        }

        return $this->render('registration/uploadcsv.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/user/supprimer/{id}", name="supprimer_user", requirements={"id"="\d+"})
     */
    public function supprimerUser(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $participantRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush($user);
        $this->addFlash('success', "L'utilisateur est maintenant supprimé");

        return $this->redirectToRoute('app_admin_liste_users');



    }

}
