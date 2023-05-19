<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\BucketListRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="app_user")
     */
    public function index(): Response
    {
        return $this->render('user/monprofil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/user/{id}", name="user_monprofil", requirements={"id"="\d+"})
     */
    public function modifier(int $id, Request $request, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $participantRepository->find($id);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $entityManager->flush($user);
        return $this->render('user/monprofil.html.twig', [
                             'registrationForm' => $form->createView(), 'user' => $user]
                        );
    }

    /**
     * @Route("/user/{id}", name="user_monprofil", requirements={"id"="\d+"})
     */
    public function afficher(int $id, Request $request, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->render();
    }
}
