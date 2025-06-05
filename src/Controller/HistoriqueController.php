<?php

// src/Controller/HistoriqueController.php
namespace App\Controller;

use App\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'app_historique_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // On récupère les historiques triés par date modification décroissante
        $historiques = $entityManager->getRepository(Historique::class)
            ->findBy([], ['dateModification' => 'DESC']);

        if ($request->isXmlHttpRequest()) {
            // Si AJAX, on ne retourne que le fragment HTML de la table
            return $this->render('historique/_search_result.html.twig', [
                'historiques' => $historiques,
            ]);
        }

        // Sinon rendu complet de la page
        return $this->render('historique/index.html.twig', [
            'historiques' => $historiques,
        ]);
    }
}
