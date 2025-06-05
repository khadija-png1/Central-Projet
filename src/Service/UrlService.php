<?php

namespace App\Service;

use App\Repository\ProjetRepository;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\NotificationService;

class UrlService
{
    public function __construct(
        private readonly ProjetRepository $projetRepository,
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationService $notificationService
    ) {}

    public function checkUrls(): array
    {
        $projets = $this->projetRepository->findAll();
        $errors = [];

        foreach ($projets as $projet) {
            $url = $projet->getUrl();
            if (!$url) {
                continue;
            }

            if (!str_starts_with($url, 'http')) {
                $url = 'http://' . $url;
            }

            try {
                $response = $this->httpClient->request('GET', $url);
                if ($response->getStatusCode() >= 400) {
                    $errors[] = $url;
                    $this->createNotification($projet, $url, "URL inaccessible (code {$response->getStatusCode()})");
                }
            } catch (\Exception $e) {
                $errors[] = $url;
                $this->createNotification($projet, $url, "Erreur lors de la vérification : " . $e->getMessage());
            }
        }

        $this->entityManager->flush();

        return $errors;
    }

    private function createNotification($projet, string $url, string $message)
    {
        // Vérifier qu'il n'y a pas déjà une notification non lue pour cette URL
        $existingNotification = $this->entityManager->getRepository(Notification::class)
            ->findOneBy([
                'projet' => $projet,
                'isRead' => false,
                'message' => $message,
            ]);

        if ($existingNotification) {
            return; // Évite les doublons
        }

        $notification = new Notification();
        $notification->setTitle("Problème avec l'URL du projet");
        $notification->setMessage("Le projet '{$projet->getNom()}' a une URL inaccessible : $url. Détail : $message");
        $notification->setCreatedAt(new \DateTime());
        $notification->setUpdatedAt(new \DateTime());
        $notification->setIsRead(false);
        $notification->setProjet($projet); // suppose que Notification a une relation avec Projet

        $this->entityManager->persist($notification);

        // Envoi email via NotificationService
        $this->notificationService->sendNotification(
            $notification->getTitle(),
            $notification->getMessage(),
            'kbouallaoui@gmail.com'
        );
    }
}
