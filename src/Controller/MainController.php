<?php

namespace App\Controller;

use App\Entity\Recherche;
use App\Form\RechercheType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class MainController extends AbstractController
{
    /**
     * @Route("/home", name="main_home")
     */
    public function home(SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request)
    {

        $date = new \DateTime();
        $date->modify('-1 month');

        $qb = $entityManager->createQueryBuilder();
        $qb->select('s')
            ->from('App\Entity\Sortie', 's')
            ->where($qb->expr()->gte('s.dateHeureDebut', ':dateHeureDebut'))
            ->setParameter('dateHeureDebut', $date);

        $sorties = $qb->getQuery()->getResult();

        // GESTION DE LA BAR DE RECHERCHE
        $rechercheForm = $this->createForm(RechercheType::class);
        $rechercheForm->handleRequest($request);

        if ($rechercheForm->isSubmitted() && $rechercheForm->isValid()) {
            $parametresDeRecherche = $rechercheForm->getData();

            // APPELLE DES FONCTIONS FILTRES - Il n'y a que le date de debut et de fin
            $campus = $parametresDeRecherche->getCampus();
            if ($campus !== null && $campus !== '') {
                //$sorties = $this->campusFilter($campus, $sorties);
            }

            $stringRecherche = $parametresDeRecherche->getStringRecherche();
            if ($stringRecherche !== null && $stringRecherche !== '') {
                //$sorties = $this->stringRechercheFilter($stringRecherche, $sorties);
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
                //$sorties = $this->checkOrganisateurFilter(, $sorties);
            }
            if ($parametresDeRecherche->isCheckInscrit()) {
                //$sorties = $this->checkInscritFilter(, $sorties);
            }
            if ($parametresDeRecherche->isCheckNonInscrit()) {
                //$sorties = $this->checkNonInscritFilter(, $sorties);
            }
            if ($parametresDeRecherche->isCheckPassee()) {
                //$sorties = $this->checkPasseeFilter(, $sorties);
            }
        }
        // RENDER DE LA PAGE
        return $this->render('main/home.html.twig',
            ['recherche' => $rechercheForm->createView(), 'sorties'=>$sorties]);
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
}