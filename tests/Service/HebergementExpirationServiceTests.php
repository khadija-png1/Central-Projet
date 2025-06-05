<?php

namespace App\Tests\Service;

use App\Entity\Hebergement;
use App\Entity\Notification;
use App\Service\HebergementExpirationService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class HebergementExpirationServiceTests extends TestCase
{
    public function testCreatesNotificationAndSendsEmailWhenExpirationIsIn15Days(): void
    {
        // Date "now" fixe pour le test (on ne peut pas forcer dans le service, donc on ajuste la dateExpiration en fonction de la date actuelle)
        $now = new \DateTimeImmutable('now');

        // Date expiration 15 jours après maintenant
        $dateExpiration = $now->modify('+15 days');

        // Mock de l'hébergement
        $hebergementMock = $this->createMock(Hebergement::class);
        $hebergementMock->method('getDateExpiration')
            ->willReturn($dateExpiration);
        $hebergementMock->method('getFournisseur')
            ->willReturn('Test Fournisseur');

        // Mock repository Hébergement
        $hebergementRepoMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $hebergementRepoMock->method('findAll')
            ->willReturn([$hebergementMock]);

        // Mock repository Notification
        $notificationRepoMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $notificationRepoMock->method('findOneBy')
            ->with([
                'hebergement' => $hebergementMock,
                'isRead' => false, // doit matcher la condition dans le service
            ])
            ->willReturn(null);

        // Mock EntityManager
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')
            ->willReturnMap([
                [Hebergement::class, $hebergementRepoMock],
                [Notification::class, $notificationRepoMock],
            ]);
        $entityManagerMock->expects($this->once())
            ->method('persist');
        $entityManagerMock->expects($this->once())
            ->method('flush');

        // Mock NotificationService
        $notificationServiceMock = $this->createMock(NotificationService::class);
        $notificationServiceMock->expects($this->once())
            ->method('sendNotification')
            ->with(
                $this->equalTo('Expiration proche'),
                $this->stringContains('Test Fournisseur'),
                $this->equalTo('kbouallaoui@gmail.com')
            );

        // Instanciation du service
        $service = new HebergementExpirationService($entityManagerMock, $notificationServiceMock);

        // Appel de la méthode sans paramètre (la date est prise dans la méthode elle-même)
        $notifications = $service->checkExpirationsAndNotify();

        // Assertions
        $this->assertCount(1, $notifications);
        $this->assertSame($hebergementMock, $notifications[0]->getHebergement());
    }
}
