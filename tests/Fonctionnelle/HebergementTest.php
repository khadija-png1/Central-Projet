<?php

// Dans un test fonctionnel (exemple avec KernelTestCase)
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Hebergement;

class HebergementTest extends KernelTestCase
{
    public function testNotificationCreatedWithRealDatabase()
    {
        self::bootKernel();
        $entityManager = self::$container->get('doctrine')->getManager();

        $hebergement = new Hebergement();
        $hebergement->setFournisseur('Test Fournisseur');
        $hebergement->setDateExpiration(new \DateTime('+15 days'));

        $entityManager->persist($hebergement);
        $entityManager->flush();

        $notificationService = self::$container->get(NotificationService::class);
        $service = new HebergementExpirationService($entityManager, $notificationService);

        $notifications = $service->checkExpirationsAndNotify();

        $this->assertCount(1, $notifications);
        $this->assertSame($hebergement, $notifications[0]->getHebergement());
    }
}
