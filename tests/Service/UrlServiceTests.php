<?php
// tests/Service/UrlServiceTests.php
namespace App\Tests\Service;

use App\Entity\Notification;
use App\Entity\Projet;
use App\Repository\ProjetRepository;
use App\Service\NotificationService;
use App\Service\UrlService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UrlServiceTests extends TestCase
{
    /**
     * Teste que lorsqu'une URL renvoie un code HTTP >= 400,
     * une notification est créée et une erreur est retournée.
     */
    public function testCheckUrlsCreatesNotificationsOnHttpError()
    {
        // Création d'un projet avec URL invalide (code 500)
        $projetMock = $this->createMock(Projet::class);
        $projetMock->method('getUrl')->willReturn('http://bad-url.com');
        $projetMock->method('getNom')->willReturn('Projet Bad');

        // ProjetRepository retourne ce projet
        $projetRepoMock = $this->createMock(ProjetRepository::class);
        $projetRepoMock->method('findAll')->willReturn([$projetMock]);

        // Réponse HTTP simulée avec code 500 (erreur serveur)
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(500);

        // Client HTTP renvoie la réponse simulée
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('request')->willReturn($responseMock);

        // Mock du repository Notification (vide => pas de doublon)
        $notificationRepoMock = $this->createMock(EntityRepository::class);
        $notificationRepoMock->method('findOneBy')->willReturn(null);

        // Mock EntityManager qui retourne le repository de notifications
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($notificationRepoMock);

        // On attend qu'une notification soit persistée
        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Notification::class));

        // On attend que flush soit appelé une fois
        $entityManagerMock->expects($this->once())
            ->method('flush');

        // NotificationService doit envoyer un mail
        $notificationServiceMock = $this->createMock(NotificationService::class);
        $notificationServiceMock->expects($this->once())
            ->method('sendNotification')
            ->with(
                $this->stringContains('Problème'),
                $this->stringContains('http://bad-url.com'),
                'kbouallaoui@gmail.com'
            );

        // Création du service avec tous les mocks
        $service = new UrlService(
            $projetRepoMock,
            $httpClientMock,
            $entityManagerMock,
            $notificationServiceMock
        );

        // Appel de la méthode à tester
        $errors = $service->checkUrls();

        // On vérifie que l'URL en erreur est bien dans la liste des erreurs
        $this->assertContains('http://bad-url.com', $errors);
    }

    /**
     * Teste que lorsqu'une exception est levée lors de la requête HTTP,
     * une notification est créée et l'URL corrigée (avec http:// ajouté) est dans les erreurs.
     */
    public function testCheckUrlsHandlesExceptionAndCreatesNotification()
    {
        $projetMock = $this->createMock(Projet::class);
        $projetMock->method('getUrl')->willReturn('fail-url.com'); // pas de http://
        $projetMock->method('getNom')->willReturn('Projet Fail');

        $projetRepoMock = $this->createMock(ProjetRepository::class);
        $projetRepoMock->method('findAll')->willReturn([$projetMock]);

        // Le client HTTP lance une exception (timeout, etc.)
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('request')
            ->willThrowException(new \Exception('Timeout'));

        $notificationRepoMock = $this->createMock(EntityRepository::class);
        $notificationRepoMock->method('findOneBy')->willReturn(null);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($notificationRepoMock);

        $entityManagerMock->expects($this->once())->method('persist');
        $entityManagerMock->expects($this->once())->method('flush');

        $notificationServiceMock = $this->createMock(NotificationService::class);
        $notificationServiceMock->expects($this->once())
            ->method('sendNotification')
            ->with(
                $this->stringContains('Problème'),
                $this->stringContains('fail-url.com'),
                'kbouallaoui@gmail.com'
            );

        $service = new UrlService(
            $projetRepoMock,
            $httpClientMock,
            $entityManagerMock,
            $notificationServiceMock
        );

        $errors = $service->checkUrls();

        // L'URL corrigée doit être dans les erreurs (avec http:// ajouté)
        $this->assertContains('http://fail-url.com', $errors);
    }

    /**
     * Teste que les projets sans URL sont ignorés sans erreur.
     */
    public function testCheckUrlsSkipsProjectsWithoutUrl()
    {
        $projetMock = $this->createMock(Projet::class);
        $projetMock->method('getUrl')->willReturn(null);

        $projetRepoMock = $this->createMock(ProjetRepository::class);
        $projetRepoMock->method('findAll')->willReturn([$projetMock]);

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $notificationServiceMock = $this->createMock(NotificationService::class);

        // Aucun appel au client HTTP ni persistance ni envoi de notif
        $httpClientMock->expects($this->never())->method('request');
        $entityManagerMock->expects($this->never())->method('persist');
        $notificationServiceMock->expects($this->never())->method('sendNotification');

        // flush est quand même appelé (dans le service)
        $entityManagerMock->expects($this->once())->method('flush');

        $service = new UrlService(
            $projetRepoMock,
            $httpClientMock,
            $entityManagerMock,
            $notificationServiceMock
        );

        $errors = $service->checkUrls();

        // Pas d'erreur puisque pas d'URL à vérifier
        $this->assertEmpty($errors);
    }
}
