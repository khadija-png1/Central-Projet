<?php

namespace App\Service;

use App\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class HistoriqueService
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function log(object $entity, string $action = 'modification'): void
    {
        $user = $this->security->getUser();
        if (!$user) return;

        $class = (new \ReflectionClass($entity))->getShortName();
        $id = method_exists($entity, 'getId') ? $entity->getId() : 'N/A';

        $historique = new Historique();
        $historique->setUser($user);
        $historique->setEntiteModifiee(sprintf('%s (ID: %s)', $class, $id));
        $historique->setDateModification(new \DateTimeImmutable());
        $historique->setAction($action); // ✅ Obligatoire

        $this->em->persist($historique);
        // ⚠️ On laisse le flush au contrôleur
    }
}
