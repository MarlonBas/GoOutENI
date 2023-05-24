<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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