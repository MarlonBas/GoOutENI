<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieType;
use App\Form\VilleFormType;
use App\Form\LieuFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/create", name="create")
     */

    public function create( Request  $request,
                            EntityManagerInterface $entityManager,
                            VilleRepository $villeRepository,
                            LieuRepository $lieuRepository,
                            EtatRepository $etatRepo) : Response
    {

        //récuperation de l'utilisateur
        $userCo = $this->getUser();

        //si utilisateur plus connecter redirection a la page de connection
        if($userCo === null){
            return $this->redirectToRoute('app_login');

        }

        $userCampus = $userCo->getCampus();

        $listeVille = $villeRepository->findAll();
        //on initialise notre variable erreur
        $error = "";

        $sortie = new Sortie();

        //Formulaire ajout de sortie
        $sortieForm = $this->createForm(SortieType ::class, $sortie);

        //récupération des données du formulaire
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            // Si utilisateur souhaite Enregister sa Sortie
            if($sortieForm->getClickedButton() && 'enregistrer'=== $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepo->find(1);
            }
            if($sortieForm->getClickedButton() && 'publier'=== $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepo->find(2);
            }
                $dateDebut = $sortieForm->get('dateHeureDebut');
                $dateFin = $sortieForm -> get('dateLimiteInscription');

                //Gestion des erreurs de date
                if ($dateFin->getData() > $dateDebut->getData()) {
                    $error = [
                        'Key' => -1,
                        'value' => "La date de limite d'inscription ne peut pas etre aprés la date de la sortie"
                    ];
                }
                if ($error == "") {
                    //On enregistrer la sortie
                    $sortie->setOrganisateur($userCo);
                    $sortie->setEtat($etat);
                    $sortie->setCampus($userCampus);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($sortie);
                    $em->flush();
                    return $this->redirectToRoute('main_home');
                }
            }





        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'listeVille' => $listeVille,
            'userCo' => $userCo,
            'userCampus' => $userCampus,
            'error' => $error,
        ]);
    }


}
