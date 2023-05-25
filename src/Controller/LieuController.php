<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/admin/lieu/list", name="list_lieu")
     */

    public function listLieu( LieuRepository $lieuRepository, VilleRepository $villeRepository,Request $request): Response
    {
        //Liste de tous les lieu
        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
        //ajouter un lieu
        $newLieu =  new Lieu();
        $lieuForm = $this->createForm(LieuFormType::class, $newLieu);
        $lieuForm->handleRequest($request);

        return $this->render('admin/listLieu.html.twig', [
            "lieu" => $lieu,
            "lieuForm" => $lieuForm->createView(),
            "ville" => $ville

    ]);

    }

    /**
     * @Route("/lieu/ajout", name="lieu_ajout")
     */

    public function ajouterLieu( LieuRepository $lieuRepository, VilleRepository $villeRepository,Request $request): Response
    {
             //ajouter un lieu
        $newLieu =  new Lieu();
        $lieuForm = $this->createForm(LieuFormType::class, $newLieu);
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newLieu);
            $em->flush();
            $this->addFlash('success', "Le nouveau lieu a été créée avec succès");
            return $this->redirectToRoute('main_home');
        }

        return $this->render('lieu/creation.html.twig', [
            "lieuForm" => $lieuForm->createView(),
            ]);

    }

    /**
     * @Route("admin/lieu/modif/{id}", name="lieu_modif")
     */
    public function modifLieu(int $id, LieuRepository $lieuRepository, VilleRepository $villeRepository,Request $request): Response
    {

        //modifier un lieu
        $lieu = $lieuRepository->find($id);
        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success', "Le lieu a été modifié avec succés");
            return $this->redirectToRoute('list_lieu');
        }
        return $this->render('lieu/creation.html.twig', [
            "lieuForm" => $lieuForm->createView(),
            "lieu" => $lieu
        ]);
    }

    /**
     * @Route("/lieux/{id}/supprimer", name="lieu_supprimer", methods={"GET"})
     */
    public function supprimer(int $id, LieuRepository $lieuRepository,EntityManagerInterface $entityManager): RedirectResponse
    {
        // Supprimer le lieu en utilisant l'EntityManager
        $lieu = $lieuRepository->find($id);
        $entityManager->remove($lieu);
        $entityManager->flush();
        $this->addFlash('success', "Le lieu a bien été supprimé");
        // Rediriger vers une autre page après la suppression
        return $this->redirectToRoute('list_lieu');
    }


    /**
     * @Route("/admin/lieu/test", name="test_lieu")
     */

    public function testLieu( LieuRepository $lieuRepository, VilleRepository $villeRepository,Request $request): Response
    {
        //Liste de tous les lieu
        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();

        //ajouter un lieu
        $newLieu =  new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $newLieu);
        $lieuForm->handleRequest($request);

        return $this->render('admin/addLieu.html.twig', [
            "lieu" => $lieu,
            "lieuForm" => $lieuForm->createView(),
            "ville" => $ville

        ]);

    }






}