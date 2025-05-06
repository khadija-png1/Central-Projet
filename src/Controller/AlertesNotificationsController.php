<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AlertesNotificationsController extends AbstractController
{
    #[Route('/alertes/notifications', name: 'app_alertes_notifications')]
    public function index(): Response
    {
        return $this->render('alertes_notifications/alertes_notifications.html.twig', [
            'controller_name' => 'AlertesNotificationsController',
        ]);
    }
}
