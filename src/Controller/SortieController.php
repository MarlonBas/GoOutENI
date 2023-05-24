<?php

namespace App\Controller;

use App\Entity\Etat;
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
use Detection\MobileDetect;
use Doctrine\DBAL\Types\TextType;
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


          if ($sortieForm->isSubmitted() && $sortieForm->isValid() ) {

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
     * @Route("/modif/{id}", name="modif", requirements={"id"="\d+"})
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
        $lieuSortie = $sortie->getLieu();
        $latitude = $lieuSortie->getLatitude();
        $longitude = $lieuSortie->getLongitude();
        $villeSortie= $lieuSortie->getVille();

        $ville = $villeRepository->findAll();
        $lieu = $lieuRepository->findAll();

        $error = "";
        $lieuForm = $this->createForm(LieuType::class);

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        dump($sortie);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //Recuperation du choix si Modification simple ou Si Publication
            if ($sortieForm->getClickedButton() && 'enregistrer' === $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepository->find(1);
            }
            if ($sortieForm->getClickedButton() && 'publier' === $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepository-> find(2);
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

                $sortie->setEtat($etat);
                $em = $this->getDoctrine()->getManager();
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('success', "La sortie a été modifiée avec succès");
                return $this->redirectToRoute('main_home');
            }
        }

        return $this->render('sortie/create.html.twig', [
            'sortie' => $sortie,
            'campus'=> $campus,
            'latitude'=> $latitude,
            'longitude'=>$longitude,
            'lieu'=>$lieu,
            'ville' =>$ville,
            'lieuSortie'=>$lieu,
            'villeSortie' =>$villeSortie,
            'sortieForm' => $sortieForm->createView(),
            'error' => $error,
            "lieuForm" => $lieuForm->createView(),

        ]);
    }
    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detail(int $id, SortieRepository $sortieRepository): Response
    {
        $mobileDetect = new MobileDetect();

        $sortie = $sortieRepository->find($id);
        $organisateur = $sortie->getOrganisateur();
        $campus = $sortie->getCampus();
        $dateSortie = $sortie->getDateHeureDebut()->format('d-m-Y H:i');
        $dateLimiteSortie = $sortie->getDateLimiteInscription()->format('d-m-Y');
        $lieu = $sortie->getLieu();
        $participants = $sortie->getParticipants()->count();

        if ($mobileDetect->isMobile()) {
            return $this->render('mobile/mobiledetailssortie.html.twig', [
                'sortie' => $sortie,
                'campus' => $campus,
                'dateLimite'=> $dateLimiteSortie,
                'dateSortie' => $dateSortie,
                'lieu' => $lieu,
                'participants' => $participants,
                'organisateur' => $organisateur,
            ]);
        }else {

            return $this->render('sortie/detail.html.twig', [
                'sortie' => $sortie,
                'campus' => $campus,
                'dateLimite' => $dateLimiteSortie,
                'dateSortie' => $dateSortie,
                'lieu' => $lieu,
                'participants' => $participants,
                'organisateur' => $organisateur,

            ]);
        }

    }

    /**
     * @Route("/annulation/{id}", name="annulation")
     */
    public function annulation(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        $dateSortie = $sortie->getDateHeureDebut()->format('d-m-Y H:i');
        $campus = $sortie->getCampus();
        $lieu = $sortie->getLieu();
        $organisateur = $sortie->getOrganisateur();


        return $this->render('sortie/annulation.html.twig', [
            'sortie'=> $sortie,
            'dateSortie' => $dateSortie,
            'campus' => $campus,
            'lieu' => $lieu,
            'organisateur' => $organisateur
            ]);


    }

    /**
     * @Route("/annulersortie/{id}", name="confirm_annulation")
     */
    public function annulerSortie(Request $request,int $id){

        // Récupérer l'entité de la sortie à annuler depuis la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $outing = $entityManager->getRepository(Sortie::class)->find($id);
        $etat= $entityManager->getRepository(Etat::class)->find(6);
        if (!$outing) {
            throw $this->createNotFoundException('La sortie n\'existe pas.');
        }
        // Récupérer le motif d'annulation depuis les données du formulaire
        $motif = $request->request->get('motif');

        // Mettre à jour les informations de la sortie annulée
        $outing->setModifAnnulation($motif);
        $outing->setEtat($etat);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();
        $this->addFlash('success', "La sortie a été annulée avec succès");
        return $this->redirectToRoute('main_home');

    }




}
