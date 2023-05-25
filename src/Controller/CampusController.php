<?php

// CampusController.php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusFormType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus/liste", name="campus_liste")
     */
    public function campusListe(): Response
    {
        // Récupérer la liste des campus depuis la base de données
        $campusRepository = $this->getDoctrine()->getRepository(Campus::class);
        $campusListe = $campusRepository->findAll();

        return $this->render('campus/liste.html.twig', [
            'campusListe' => $campusListe,
        ]);
    }

    /**
     * @Route("/campus/ajouter", name="campus_ajout", methods={"GET", "POST"})
     */
    public function campusAjout(Request $request): Response
    {
        $campus = new Campus();

        $form = $this->createForm(CampusFormType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer le nouveau campus dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->addFlash('success', "Le nouveau lieu a été créée avec succès");


            // Rediriger ou afficher un message de succès
            // ...

            return $this->redirectToRoute('campus_liste');
        }

        return $this->render('campus/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/campus/modifier/{id}", name="campus_modif", methods={"GET", "POST"})
     */
    public function campusModification(int $id,Request $request, CampusRepository $campusRepository ): Response
    {
        $campus= $campusRepository->find($id);
        $form = $this->createForm(CampusFormType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le campus dans la base de données
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "La ville modifié avec succès");
            // Rediriger ou afficher un message de succès
            // ...

            return $this->redirectToRoute('campus_liste');
        }

        return $this->render('campus/ajout.html.twig', [
            'form' => $form->createView(),
            'campus' => $campus,
        ]);
    }

    /**
     * @Route("/campus/supprimer/{id}", name="campus_supprimer")
     */
    public function campusSuppression(int $id, Request $request, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campus);
            $entityManager->flush();
          $this->addFlash('success', 'Le campus a été supprimée avec succès.');


        // Rediriger ou afficher un message de succès
        // ...

        return $this->redirectToRoute('campus_liste');
    }
}