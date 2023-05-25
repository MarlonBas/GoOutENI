<?php

namespace App\Controller;


use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

class UserController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;


    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
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
     * @Route("/user/{id}", name="user_monprofil", requirements={"id"="\d+"})
     * @throws ResetPasswordExceptionInterface
     */
    public function modifier(int $id, Request $request, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $participantRepository->find($id);
        $isGrantedUser = $this->isGranted('ROLE_ADMIN');

        $form = $this->createForm(RegistrationFormType::class, $user,
            ["isGrantedUser" => $isGrantedUser]);
        $form->handleRequest($request);


            $uploadedFile = $form->get('image')->getData();
        //   if ($form->isSubmitted() && $form->isValid()) {

            if ($uploadedFile) {

                $newFilename = uniqid() . '.' . $uploadedFile->getClientOriginalExtension();

                $targetDirectory = $this->getParameter('upload_directory');
                $uploadedFile->move($targetDirectory, $newFilename);
                $user->setImage($newFilename);
            }
           $this->addFlash('success', 'Modification de profil faite');


      //      }
                $entityManager->flush($user);


        return $this->render('user/monprofil.html.twig', [
                    'registrationForm' => $form->createView(), 'user' => $user]
            );
        }



    /**
     * @Route("/user/participant/{id}", name="user_profil_participant", requirements={"id"="\d+"})
     */
    public function afficherProfil(int $id, ParticipantRepository $participantRepository): Response
    {
        $user= $participantRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("oh no!!!, ce participant n'existe pas");
        }
        return $this -> render('user/profilparticipant.html.twig', ['user' => $user]);
    }
    /**
     * @Route("/home/inscription/{id}", name="user_inscription", requirements={"id"="\d+"})
     */
    public function inscription(EntityManagerInterface $entityManager, int $id, SortieRepository $sortieRepository, ParticipantRepository $participantRepository) {


        $user = $this->tokenStorage->getToken()->getUser();
        $userId = $user->getId();
        $user=$participantRepository->find($userId);
        $sortie =$sortieRepository->find($id);

        if($sortie->getEtat()->getId()==2) {

            $user->addSorty($sortie);
            $sortie->addParticipant($user);

            $entityManager->flush($sortie);
            $entityManager->flush($user);
            $this->addFlash('success', "Vous êtes inscrits");


        } else {
            $this->addFlash('danger', "Les inscriptions ne sont pas ouvertes");

        }

        return $this->redirectToRoute('main_home');



    }
    /**
     * @Route("/home/desister/{id}", name="user_desistement", requirements={"id"="\d+"})
     */
    public function desistement(int $id, EntityManagerInterface $entityManager
, SortieRepository $sortieRepository, ParticipantRepository $participantRepository) {


        $user = $this->tokenStorage->getToken()->getUser();


            $userId = $user->getId();

            $user=$participantRepository->find($userId);

            $sortie =$sortieRepository->find($id);

            $user->removeSorty($sortie);
            $sortie->removeParticipant($user);
        $this->addFlash('danger', "Vous vous êtes désisté de la sortie");



        $entityManager->flush($sortie);
        $entityManager->flush($user);

        return $this->redirectToRoute('main_home');
    }

}
