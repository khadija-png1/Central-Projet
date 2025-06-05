<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setRoles(['ROLE_USER']);
            // Attention : mot de passe en clair ici, à remplacer par un hash en prod
            $user->setPassword('password'); 
            $user->setStatut('actif');
            $user->setNom("Nom{$i}");
            $user->setPrenom("Prenom{$i}");
            $user->setCreated(new \DateTime());
            $user->setUpdated(new \DateTime());

            $manager->persist($user);

            // Ajout d'une référence unique pour chaque user, clé : user-1, user-2, ...
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }

        $manager->flush();
    }
}
