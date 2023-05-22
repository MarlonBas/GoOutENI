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
    public function home(SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {

        $date = new \DateTime();
        $date->modify('-1 month');

        $qb = $entityManager->createQueryBuilder();
        $qb->select('s')
            ->from('App\Entity\Sortie', 's')
            ->where($qb->expr()->gte('s.dateHeureDebut', ':dateHeureDebut'))
            ->setParameter('dateHeureDebut', $date);

        $sorties = $qb->getQuery()->getResult();


        $rechercheForm = $this->createForm(RechercheType::class);
        return $this->render('main/home.html.twig',
            ['recherche' => $rechercheForm->createView(), 'sorties'=>$sorties]);
    }

}