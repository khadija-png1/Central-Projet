<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository; // <-- la bonne classe à mocker
use App\Service\NotificationService;
use Symfony\Component\Mime\Email;

class NotificationServiceTests extends TestCase
{
    public function testSendNotification(): void
    {
        $mailerMock = $this->createMock(MailerInterface::class);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) {
                return $email->getTo()[0]->getAddress() === 'test@example.com'
                    && $email->getSubject() === 'Sujet test'
                    && $email->getTextBody() === 'Contenu test';
            }));

        $notificationService = new NotificationService($mailerMock, $entityManagerMock);

        $notificationService->sendNotification(
            'Sujet test',
            'Contenu test',
            'test@example.com'
        );
    }

    public function testCountUnreadNotifications(): void
    {
        // Mock de EntityRepository, constructeur désactivé
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['count'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('count')
            ->with(['isRead' => false])
            ->willReturn(5);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(\App\Entity\Notification::class)
            ->willReturn($repositoryMock);

        $mailerMock = $this->createMock(MailerInterface::class);

        $service = new NotificationService($mailerMock, $entityManagerMock);

        $count = $service->countUnreadNotifications();

        $this->assertEquals(5, $count);
    }

    public function testMarkAsRead(): void
    {
        $notification = $this->createMock(\App\Entity\Notification::class);

        $notification->expects($this->once())
            ->method('isRead')
            ->willReturn(false);

        $notification->expects($this->once())
            ->method('setIsRead')
            ->with(true);

        $notification->expects($this->once())
            ->method('setUpdatedAt')
            ->with($this->isInstanceOf(\DateTime::class));

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('find')
            ->with(123)
            ->willReturn($notification);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(\App\Entity\Notification::class)
            ->willReturn($repositoryMock);

        $entityManagerMock->expects($this->once())
            ->method('flush');

        $mailerMock = $this->createMock(MailerInterface::class);

        $service = new NotificationService($mailerMock, $entityManagerMock);

        $result = $service->markAsRead(123);

        $this->assertSame($notification, $result);
    }
}
