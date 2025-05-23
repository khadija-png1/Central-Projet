<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Hebergement;
use App\Entity\Projet;
use App\Entity\Technologie;
use App\Entity\Historique;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'app_historique_index', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager ,  ManagerRegistry $doctrine, Request $request): Response
    {
        // Vérification utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Récupération des entités
   
        $hebergements = $doctrine->getRepository(Hebergement::class)->findAll();
        $projets      = $doctrine->getRepository(Projet::class)->findAll();
        $technologies = $doctrine->getRepository(Technologie::class)->findAll();

        // Enregistrement des historiques (optimisé)
        $entityManager = $doctrine->getManager();
        $this->saveHistorique($entityManager, $user, 'Hebergement', $hebergements);
        $this->saveHistorique($entityManager, $user, 'Projet', $projets);
        $this->saveHistorique($entityManager, $user, 'Technologie', $technologies);

        // Récupération des historiques
        $historiques = $doctrine->getRepository(Historique::class)->findAll();

        // Vérifie si c'est une requête AJAX (POST)
        if ($request->isXmlHttpRequest()) {
            return $this->render('historique/_search_result.html.twig', [
                'historiques' => $historiques,
            ]);
        }

        // Affichage de la page historique
        return $this->render('historique/index.html.twig', [
  
            'historiques' => $historiques,
        ]);
    }

    private function saveHistorique($entityManager, $user, $entiteModifiee, $entities): void
    {
        $existingHistories = $entityManager->createQueryBuilder()
            ->select('h.entiteModifiee')
            ->from(Historique::class, 'h')
            ->where('h.user = :user')
            ->andWhere('h.entiteModifiee IN (:ids)')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $existingIds = array_map(fn($item) => $item['entiteModifiee'], $existingHistories);

        foreach ($entities as $entity) {
            $entityId = $entiteModifiee . '-' . $entity->getId();

            if (!in_array($entityId, $existingIds)) {
                $historique = new Historique();
                $historique->setDateModification(new \DateTime());
                $historique->setEntiteModifiee($entityId);
                $historique->setUser($user);
                $entityManager->persist($historique);
            }
        }

        $entityManager->flush();
    }
}
