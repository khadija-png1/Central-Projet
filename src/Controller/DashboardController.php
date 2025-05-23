<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Projet;
use App\Entity\Developpeur;
use App\Entity\Technologie;
use App\Entity\hebergement;
 
use Symfony\Component\HttpFoundation\Request;
final class DashboardController extends AbstractController{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ManagerRegistry $doctrine): Response
    
    {

       
        $repository = $doctrine->getRepository(Projet::class);
        $projets = $repository->findAll();
        $repository = $doctrine->getRepository(Developpeur::class);
        $developpeurs = $repository->findAll();
        $repository = $doctrine->getRepository(Technologie::class);
        $technologies = $repository->findAll();
        $repository = $doctrine->getRepository(hebergement::class);
        $hebergements = $repository->findAll();

        return $this->render('dashboard/dashboard.html.twig', [
            'projets'      => $projets,
            'developpeurs' => $developpeurs,
            'technologies' => $technologies,
            'hebergements' => $hebergements,
        ]);
    }
}
