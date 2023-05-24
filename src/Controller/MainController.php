<?php

namespace App\Controller;

use App\Entity\Recherche;
use App\Form\RechercheType;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Detection\MobileDetect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class MainController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("/home", name="main_home")
     */
    public function home(SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $mobileDetect = new MobileDetect();


        $date = new \DateTime();
        $date->modify('-1 month');

        $qb = $entityManager->createQueryBuilder();
        $qb->select('s')
            ->from('App\Entity\Sortie', 's')
            ->where($qb->expr()->gte('s.dateHeureDebut', ':dateHeureDebut'))
            ->setParameter('dateHeureDebut', $date);

        $sorties = $qb->getQuery()->getResult();

        //GESTION DE LA SESSION ET INFO USER
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        $sortiesInscrit = $user->getSorties()->toArray();
        $sortiesOrganisees = $user->getsortiesOrganisees()->toArray();


        // GESTION DE LA BAR DE RECHERCHE
        $rechercheForm = $this->createForm(RechercheType::class);
        $rechercheForm->handleRequest($request);

        if ($rechercheForm->isSubmitted() && $rechercheForm->isValid()) {
            $parametresDeRecherche = $rechercheForm->getData();
            dump($parametresDeRecherche);

            // APPELLE DES FONCTIONS FILTRES - (avec la fonction array_filter() )
            $campus = $parametresDeRecherche->getCampus();
            if ($campus !== null && $campus !== '') {
                $sorties = $this->campusFilter($campus, $sorties);
            }

            $stringRecherche = $parametresDeRecherche->getStringRecherche();
            if ($stringRecherche !== null && $stringRecherche !== '') {
                $sorties = $this->stringRechercheFilter($stringRecherche, $sorties);
            }

            $dateDebut = $parametresDeRecherche->getDateDebut();
            if ($dateDebut !== null && $dateDebut !== '') {
                $sorties = $this->dateDebutFilter($dateDebut, $sorties);
            }

            $dateFin = $parametresDeRecherche->getDateFin();
            if ($dateFin !== null && $dateFin !== '') {
                $sorties = $this->dateFinFilter($dateFin, $sorties);
            }

            if ($parametresDeRecherche->isCheckOrganisateur()) {
                $sorties = $this->checkOrganisateurFilter($sortiesOrganisees, $sorties);
            }

            if ($parametresDeRecherche->isCheckInscrit()) {
                $sorties = $this->checkInscritFilter($sortiesInscrit, $sorties);
            }
            if ($parametresDeRecherche->isCheckNonInscrit()) {
                $sorties = $this->checkNonInscritFilter($sortiesInscrit, $sorties);
            }
            if ($parametresDeRecherche->isCheckPassee()) {
                $sorties = $this->checkPasseeFilter($sorties);
            }
        }
        if($mobileDetect->isMobile()){
            return $this->render('main/home.html.twig',
                ['sorties'=>$sorties]);
        }else {


            // RENDER DE LA PAGE
            return $this->render('main/home.html.twig',
                ['recherche' => $rechercheForm->createView(), 'sorties' => $sorties]);
        }
    }

    private function campusFilter($campus, $sorties)
    {
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($campus) {
            return $sortie->getCampus() === $campus;
        });
        return $sortiesFiltres;
    }

    private function stringRechercheFilter($stringRecherche, $sorties)
    {
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($stringRecherche) {
            $nomContains = stripos($sortie->getNom(), $stringRecherche) !== false;
            $infosSortieContains = stripos($sortie->getInfosSortie(), $stringRecherche) !== false;

            return $nomContains || $infosSortieContains;
        });
        return $sortiesFiltres;
    }

    private function dateDebutFilter($dateDebut, $sorties)
    {
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($dateDebut) {
            return $sortie->getDateHeureDebut() >= $dateDebut;
        });
        return $sortiesFiltres;
    }

    private function dateFinFilter($dateFin, $sorties)
    {
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($dateFin) {
            return $sortie->getDateHeureDebut() <= $dateFin;
        });
        return $sortiesFiltres;
    }

    private function checkOrganisateurFilter($sortiesOrganisees, $sorties)
    {
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($sortiesOrganisees) {
            return in_array($sortie, $sortiesOrganisees);
        });
        return $sortiesFiltres;
    }

    private function checkInscritFilter($sortiesInscrit, $sorties)
    {
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($sortiesInscrit) {
            return in_array($sortie, $sortiesInscrit);
        });
        return $sortiesFiltres;
    }

    private function checkNonInscritFilter($sortiesInscrit, $sorties)
    {
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($sortiesInscrit) {
            return !in_array($sortie, $sortiesInscrit);
        });
        return $sortiesFiltres;
    }

    private function checkPasseeFilter($sorties)
    {
        $ajd = date('Y-m-d');
        $sortiesFiltres = array_filter($sorties, function ($sortie) use ($ajd) {
            return $sortie->getDateHeureDebut() < $ajd;
        });
        return $sortiesFiltres;
    }
}