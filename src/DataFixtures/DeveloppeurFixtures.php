<?php

namespace App\DataFixtures;

use App\Entity\Developpeur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class DeveloppeurFixtures extends Fixture implements DependentFixtureInterface
{
    public const DEVELOPPEUR_REFERENCE = 'dev-';

    public function load(ObjectManager $manager): void
    {
        // Exemple de noms et prénoms
        $noms = ['Dupont', 'Martin', 'Durand', 'Moreau', 'Petit'];
        $prenoms = ['Alice', 'Bob', 'Charlie', 'David', 'Eve'];

        for ($i = 1; $i <= 15; $i++) {
            $developpeur = new Developpeur();
            
            $developpeur->setNom($noms[array_rand($noms)]);
            $developpeur->setPrenom($prenoms[array_rand($prenoms)]);
            $developpeur->setEmail("dev{$i}@example.com");
            $developpeur->setTelephone('060000000' . $i);

            // Récupère un User existant depuis UserFixtures (obligatoire)
            $user = $this->getReference('user-' . $i, \App\Entity\User::class);

            $developpeur->setUser($user);

            $manager->persist($developpeur);

            // On ajoute une référence pour relier aux projets
            $this->addReference(self::DEVELOPPEUR_REFERENCE . $i, $developpeur);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,  // dépend de la fixture User pour les users existants
        ];
    }
}
