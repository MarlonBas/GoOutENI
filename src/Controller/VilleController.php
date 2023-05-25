<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleFormType;
use App\Repository\VilleRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VilleController extends AbstractController
{
    /**
     * @Route("/admin/ville/list", name="ville_list")
     */

    public function listLieu( VilleRepository $villeRepository): Response
    {
        //Liste de tous les lieu
        $ville = $villeRepository->findAll();

        return $this->render('admin/listVille.html.twig', [
            "ville" => $ville,

        ]);

    }

    /**
     * @Route("/admin/ville/ajout", name="ville_ajout")
     */
    public function ajouterVille(Request $request): Response
    {
        //ajouter un lieu
        $newVille =  new Ville();
        $villeForm = $this->createForm(VilleFormType::class, $newVille);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newVille);
            $em->flush();
            $this->addFlash('success', "Le nouveau lieu a été créée avec succès");
            return $this->redirectToRoute('ville_list');
        }

        return $this->render('ville/creation.html.twig', [
            "villeForm" => $villeForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/ville/modif/{id}", name="ville_modif")
     */
    public function modifVille(int $id, VilleRepository $villeRepository,Request $request): Response
    {
        //ajouter un lieu

        $newVille = $villeRepository->find($id);
        $villeForm = $this->createForm(VilleFormType::class, $newVille);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newVille);
            $em->flush();
            $this->addFlash('success', "La ville modifié avec succès");
            return $this->redirectToRoute('ville_list');
        }

        return $this->render('ville/creation.html.twig', [
            "villeForm" => $villeForm->createView(),
            'ville' => $newVille,
        ]);
    }




    /**
     * @Route("/ville/supprimer/{id}", name="ville_supprimer", methods={"GET"})
     */
    public function supprimerVille(int $id, VilleRepository $villeRepository): RedirectResponse
    {
        $ville = $villeRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ville);
        $entityManager->flush();
        $this->addFlash('success', 'La ville a été supprimée avec succès.');

        return $this->redirectToRoute('ville_list');
    }







}