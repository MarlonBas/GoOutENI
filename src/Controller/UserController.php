<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Form\UploadImageFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
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
     */
    public function modifier(int $id, Request $request, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $participantRepository->find($id);
        $isGrantedUser = $this->isGranted('ROLE_ADMIN');

        $form = $this->createForm(RegistrationFormType::class, $user,
            ["isGrantedUser" => $isGrantedUser]);
        $form->handleRequest($request);

      //  if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {

                $newFilename = uniqid() . '.' . $uploadedFile->getClientOriginalExtension();

                $targetDirectory = $this->getParameter('upload_directory');
                $uploadedFile->move($targetDirectory, $newFilename);
                $user->setImage($newFilename);
            }
            $entityManager->flush($user);
            $this->addFlash('success', "Modification de profil faite");


      //  }

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
}
