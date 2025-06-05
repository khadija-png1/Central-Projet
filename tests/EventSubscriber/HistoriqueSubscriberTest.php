<?php

use PHPUnit\Framework\TestCase;
use App\EventSubscriber\HistoriqueSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Historique;
use App\Entity\User;

// ✅ Classe factice pour contourner la méthode finale getEntityManager
class FakeOnFlushEventArgs extends OnFlushEventArgs
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}

class HistoriqueSubscriberTest extends TestCase
{
    public function testOnFlushWithNoUser()
    {
        // Mock Security pour retourner null (pas d'utilisateur connecté)
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $subscriber = new HistoriqueSubscriber($security);

        // Mock EntityManagerInterface
        $entityManager = $this->createMock(EntityManagerInterface::class);
        // Mock UnitOfWork
        $uow = $this->createMock(UnitOfWork::class);
        $entityManager->method('getUnitOfWork')->willReturn($uow);

        // Vérifie que persist() n'est jamais appelé
        $entityManager->expects($this->never())->method('persist');

        // Création de l'event avec entité simulée
        $onFlushEventArgs = new FakeOnFlushEventArgs($entityManager);

        // Appelle la méthode testée
        $subscriber->onFlush($onFlushEventArgs);
    }

    public function testOnFlushWithUserAndEntities()
    {
        $user = $this->createMock(User::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $subscriber = new HistoriqueSubscriber($security);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $uow = $this->createMock(UnitOfWork::class);

        // Entité factice avec méthode getId()
        $entity = new class {
            public function getId() {
                return 123;
            }
        };

        // Simule des entités modifiées
        $uow->method('getScheduledEntityUpdates')->willReturn([$entity]);
        $uow->method('getScheduledEntityInsertions')->willReturn([]);
        $uow->method('getScheduledEntityDeletions')->willReturn([]);

        $entityManager->method('getUnitOfWork')->willReturn($uow);

        // Mock ClassMetadata
        $classMetadata = $this->createMock(ClassMetadata::class);
        $entityManager->method('getClassMetadata')
            ->with(Historique::class)
            ->willReturn($classMetadata);

        // S'assurer que persist() et computeChangeSet() sont bien appelés
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Historique::class));

        $uow->expects($this->once())
            ->method('computeChangeSet')
            ->with($classMetadata, $this->isInstanceOf(Historique::class));

        $onFlushEventArgs = new FakeOnFlushEventArgs($entityManager);

        $subscriber->onFlush($onFlushEventArgs);
    }
}
