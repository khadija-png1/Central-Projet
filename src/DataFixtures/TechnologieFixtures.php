<?php

namespace App\DataFixtures;

use App\Entity\Technologie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TechnologieFixtures extends Fixture
{
    public const TECHNOLOGIE_REFERENCE = 'tech-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $technologie = new Technologie();
            $technologie->setNom("Technologie {$i}");  // ou une vraie liste de noms
            $technologie->setCreated(new \DateTime());
            $technologie->setUpdated(new \DateTime());

            $manager->persist($technologie);

            // Ajout d'une référence pour chaque technologie
            $this->addReference(self::TECHNOLOGIE_REFERENCE . $i, $technologie);
        }

        $manager->flush();
    }
}
