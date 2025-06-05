<?php

namespace App\EventSubscriber;

use App\Entity\Historique;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: Events::onFlush, priority: 0)]
class HistoriqueSubscriber
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $uow = $entityManager->getUnitOfWork();
    
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }
    
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Historique) {
                continue;
            }
    
            $className = (new \ReflectionClass($entity))->getShortName();
            $id = method_exists($entity, 'getId') ? $entity->getId() : 'N/A';
    
            $historique = new Historique();
            $historique->setUser($user);
            $historique->setEntiteModifiee(sprintf('%s (ID: %s)', $className, $id));
            $historique->setDateModification(new \DateTimeImmutable());
    
            $entityManager->persist($historique);
    
            $meta = $entityManager->getClassMetadata(Historique::class);
            $uow->computeChangeSet($meta, $historique);
        }
    }
      
}
