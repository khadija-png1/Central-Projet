<?php

namespace App\Tests\Entity;

use App\Entity\Notification;
use App\Entity\Hebergement;
use App\Entity\Projet;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testNotificationEntity()
    {
        $notification = new Notification();
        $hebergement = $this->createMock(Hebergement::class);
        $projet = $this->createMock(Projet::class);
        $createdAt = new \DateTimeImmutable('2024-01-01 12:00:00');
        $updatedAt = new \DateTimeImmutable('2024-01-02 14:00:00');

        $notification->setTitle('Titre de la notification');
        $notification->setMessage('Contenu de la notification');
        $notification->setCreatedAt($createdAt);
        $notification->setUpdatedAt($updatedAt);
        $notification->setIsRead(true);
        $notification->setHebergement($hebergement);
        $notification->setProjet($projet);

        $this->assertSame('Titre de la notification', $notification->getTitle());
        $this->assertSame('Contenu de la notification', $notification->getMessage());
        $this->assertSame($createdAt, $notification->getCreatedAt());
        $this->assertSame($updatedAt, $notification->getUpdatedAt());
        $this->assertTrue($notification->isRead());
        $this->assertSame($hebergement, $notification->getHebergement());
        $this->assertSame($projet, $notification->getProjet());
    }
}
