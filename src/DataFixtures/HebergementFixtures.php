<?php

namespace App\DataFixtures;

use App\Entity\Hebergement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HebergementFixtures extends Fixture
{
    public const HEBERGEMENT_REFERENCE = 'hebergement-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $hebergement = new Hebergement();
            $hebergement->setFournisseur("Fournisseur {$i}");
            $hebergement->setType($i % 2 === 0 ? 'Cloud' : 'Dedicated');
            $hebergement->setUrl("https://fournisseur{$i}.example.com");

            if ($i === 1) {
                // Premier hébergement expire dans 15 jours
                $hebergement->setDateExpiration(new \DateTime('+15 days'));
            } else {
                // Les autres expirent à des dates différentes (ex: +i mois)
                $hebergement->setDateExpiration(new \DateTime("+{$i} months"));
            }

            $hebergement->setCreated(new \DateTime());
            $hebergement->setUpdated(new \DateTime());

            $manager->persist($hebergement);

            // Ajouter référence pour liaison dans NotificationFixtures
            $this->addReference(self::HEBERGEMENT_REFERENCE . $i, $hebergement);
        }

        $manager->flush();
    }
}
