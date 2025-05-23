<?php

namespace App\Twig;

use App\Repository\NotificationRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getNotification', [$this, 'getNotification']),
        ];
    }

    public function getNotification()
    {
        $notifications = $this->notificationRepository->findBy(['isRead' => 0]);

        return $notifications;
    }
}
