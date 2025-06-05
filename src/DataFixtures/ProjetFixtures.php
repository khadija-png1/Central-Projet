<?php

namespace App\DataFixtures;

use App\Entity\Projet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProjetFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROJET_REFERENCE = 'projet-';

    public function load(ObjectManager $manager): void
    {
        $statuses = ['à faire', 'en cours', 'terminé'];
        $types = ['Web', 'Mobile', 'Desktop', 'API'];

        for ($i = 1; $i <= 20; $i++) {  // Création de 20 projets
            $projet = new Projet();
            $projet->setNom("Projet numéro {$i}");
            $projet->setType($types[array_rand($types)]);
            $projet->setDescription("Description du projet numéro {$i}.");
            $projet->setAccesCodeSource("https://git.example.com/projet{$i}");
            $projet->setAccesEnvironnement("https://env.example.com/projet{$i}");
            $projet->setStatus($statuses[array_rand($statuses)]);
            $projet->setUrl("https://projet{$i}.example.com");

            // Ajout de technologies (par exemple 2 par projet)
            for ($j = 1; $j <= 2; $j++) {
                $techIndex = ($i + $j) % 10 ?: 1; // supposer 10 techs max
                $technologie = $this->getReference('tech-' . $techIndex, \App\Entity\Technologie::class);

                $projet->addTechnologie($technologie);
            }

            // Ajout de développeurs (1 à 3 par projet)
            $nbDev = rand(1, 3);
            for ($k = 1; $k <= $nbDev; $k++) {
                $devIndex = ($i + $k) % 15 ?: 1; // supposer 15 dev max
                $developpeur = $this->getReference('dev-' . $devIndex, \App\Entity\Developpeur::class);

                $projet->addDeveloppeur($developpeur);
            }

            // Ajout d'hébergements (1 à 2 par projet)
            $nbHeb = rand(1, 2);
            for ($h = 1; $h <= $nbHeb; $h++) {
                $hebIndex = ($i + $h) % 50 ?: 1; // supposer 50 hébergements max
                $hebergement = $this->getReference('hebergement-' . $hebIndex, \App\Entity\Hebergement::class);

                $projet->addHebergement($hebergement);
            }

            $manager->persist($projet);
            $this->addReference(self::PROJET_REFERENCE . $i, $projet);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TechnologieFixtures::class,
            DeveloppeurFixtures::class, // ou UserFixtures selon ton cas
            HebergementFixtures::class,
        ];
    }
}
