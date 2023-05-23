<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleFormType;
use App\Form\LieuFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
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

        $campus = $userCo->getCampus();
        $ville = $listeVille = $villeRepository->findAll();
        $lieu = $lieuRepository->findAll();

        //on initialise notre variable erreur
        $error = "";
        $sortie = new Sortie();
        $newLieu =  new Lieu();

        //Formulaire ajout de sortie
        $sortieForm = $this->createForm(SortieType ::class, $sortie);
        $lieuForm = $this->createForm(LieuType::class, $newLieu);

        //récupération des données du formulaire
        $sortieForm->handleRequest($request);
        $lieuForm->handleRequest($request);

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
                    $sortie->setCampus($campus);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', "La sortie a été créée avec succès");
                    return $this->redirectToRoute('main_home');
                }
            }


        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie,
            'listeVille' => $listeVille,
            'userCo' => $userCo,
            'campus' => $campus,
            'error' => $error,
            "lieu" => $lieu,
            "lieuForm" => $lieuForm->createView(),
            "ville" => $ville

        ]);
    }

    /**
     * @Route("/modif/{id}", name="modif")
     */
    public function modifier(int $id, Request $request,
                             EtatRepository $etatRepository,
                             VilleRepository $villeRepository,
                             LieuRepository $lieuRepository,
                            SortieRepository $sortieRepository): Response
    {
        $userCo =  $this->getUser();
        $sortie = $sortieRepository->find($id);
        $userOrganisateur = $sortie->getOrganisateur();

        if($userCo != $userOrganisateur){
            $this->addFlash('danger', "Redirection vous n'avez pas accés a cette page");
            return $this->redirectToRoute('main_home');
        }

        $campus = $sortie->getOrganisateur()->getCampus();
        $lieu = $sortie->getLieu();
        $latitude = $lieu->getLatitude();
        $longitude = $lieu->getLongitude();
        $ville = $lieu->getVille();

        $error = "";
//        $newLieu = new Lieu();
//        $lieuform = $this->createForm(LieuFormType::class, $newLieu);

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //Recuperation du choix si Modification simple ou Si Publication
            if ($sortieForm->getClickedButton() && 'enregistrer' === $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepository->find(1);
            }
            if ($sortieForm->getClickedButton() && 'publier' === $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepository > find(2);
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été modifiée avec succès");
            return $this->redirectToRoute('main_home');

        }

        return $this->render('sortie/create.html.twig', [
            'sortie' => $sortie,
            'campus'=> $campus,
            'latitude'=> $latitude,
            'longitude'=>$longitude,
            'lieu'=>$lieu,
            'ville' =>$ville,
            'sortieForm' => $sortieForm->createView(),
            'error' => $error


        ]);


    }




}
