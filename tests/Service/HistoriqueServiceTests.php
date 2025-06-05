<?php

namespace App\Tests\Service;

use App\Entity\Historique;
use App\Entity\User;
use App\Service\HistoriqueService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

// ✅ Classe d'entité factice nommée pour éviter un nom anonyme
class DummyEntity {
    public function getId() { return 42; }
}

class HistoriqueServiceTests extends TestCase
{
    public function testLogPersistsHistorique(): void
    {
        $userMock = $this->createMock(User::class);

        $securityMock = $this->createMock(Security::class);
        $securityMock->method('getUser')->willReturn($userMock);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Historique $historique) use ($userMock) {
                return $historique->getUser() === $userMock
                    && $historique->getEntiteModifiee() === 'DummyEntity (ID: 42)'
                    && $historique->getAction() === 'modification'
                    && $historique->getDateModification() instanceof \DateTimeImmutable;
            }));

        $entity = new DummyEntity();

        $service = new HistoriqueService($securityMock, $entityManagerMock);
        $service->log($entity, 'modification');
    }

    public function testLogWithoutUserDoesNothing(): void
    {
        $securityMock = $this->createMock(Security::class);
        $securityMock->method('getUser')->willReturn(null);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->never())->method('persist');

        $entity = new DummyEntity();

        $service = new HistoriqueService($securityMock, $entityManagerMock);
        $service->log($entity, 'suppression');
    }
}
