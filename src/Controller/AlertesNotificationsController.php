<?php

// src/Controller/AlertesNotificationsController.php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\HebergementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Service\HebergementExpirationService;
use Symfony\Component\Routing\Annotation\Route;

class AlertesNotificationsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private HebergementExpirationService $hebergementExpirationService;

    public function __construct(EntityManagerInterface $entityManager, HebergementExpirationService $hebergementExpirationService)
    {
        $this->entityManager = $entityManager;
        $this->hebergementExpirationService = $hebergementExpirationService;
    }

    #[Route('/check-expirations', name: 'app_check_expirations')]
    public function checkExpirations(): JsonResponse
    {
        // Lancer la vérification et la création des notifications + envoi mail
        $notifications = $this->hebergementExpirationService->checkExpirationsAndNotify();

        return new JsonResponse([
            'success' => true,
            'message' => count($notifications) . ' notification(s) créée(s).',
            'notificationsCount' => count($notifications),
        ]);
    }

    #[Route('/AlertesNotifications', name: 'app_alertes_notifications')]
    public function showNotifications(): Response
    {
        // Récupérer toutes les notifications non lues (sans relancer la vérification)
        $notifications = $this->entityManager->getRepository(Notification::class)->findBy(['isRead' => false]);

        return $this->render('AlertesNotifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notification/{id}/mark-read', name: 'notification_mark_read', methods: ['POST'])]
    public function markAsRead(int $id, HebergementRepository $hebergementRepository): JsonResponse
    {
        $notification = $this->entityManager->getRepository(Notification::class)->find($id);

        if (!$notification) {
            $this->addFlash('error', 'Notification non trouvée');
            return new JsonResponse(['status' => 'error']);
        }

        if (!$notification->isRead()) {
            $notification->setIsRead(true);
            $notification->setUpdatedAt(new \DateTime());

            //Read another
            /* $hebergement = $hebergementRepository->findOneBy(['id' => $notification->getHebergement()]);

            foreach ($hebergement->getNotifications() as $notification) {
                $notification->setIsRead(true);
                $notification->setUpdatedAt(new \DateTime());
            } */

            $this->entityManager->flush();
        }

        return new JsonResponse(['status' => 'success']);
    }
}
