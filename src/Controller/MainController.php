<?php

namespace App\Controller;

use App\Entity\Recherche;
use App\Form\RechercheType;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class MainController extends AbstractController
{
    /**
     * @Route("/home", name="main_home")
     */
    public function home(Request $request)
    {
        $rechercheForm = $this->createForm(RechercheType::class);
        return $this->render('main/home.html.twig',
            ['recherche' => $rechercheForm->createView()]);
    }

}