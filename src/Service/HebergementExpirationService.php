<?php
namespace App\Service;

use App\Entity\Hebergement;
use Doctrine\ORM\EntityManagerInterface;

class HebergementExpirationService
 

{
    private EntityManagerInterface $entityManager;
    private NotificationService $notificationService;

    public function __construct(EntityManagerInterface $entityManager, NotificationService $notificationService)
    {
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
    }

    public function checkExpirationsAndNotify(): voidé
    {
        // Récupérer tous les hébergements
        $hebergements = $this->entityManager->getRepository(Hebergement::class)->findAll();

        foreach ($hebergements as $hebergement) {
            $dateExpiration = $hebergement->getDateExpiration();
            $aujourdhui = new \DateTime();
            $interval = $aujourdhui->diff($dateExpiration);

            // Si l'expiration est dans 15 jours, envoyer une notification
            if ($interval->invert === 0 && $interval->days === 15) {
                $this->notificationService->sendNotification(
                    'Expiration proche',
                    'L\'hébergement ' . $hebergement->getNom() . ' expire dans 15 jours.',
                    'kbouallaoui@gmail.com' // Ton email ici
                );
            }
        }
    }
}
