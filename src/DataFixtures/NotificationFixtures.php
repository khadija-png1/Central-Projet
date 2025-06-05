<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $notification = new Notification();
            $notification->setTitle("Notification {$i}");
            $notification->setMessage("Message de notification numéro {$i}.");
            $notification->setCreatedAt(new \DateTime());
            $notification->setUpdatedAt(new \DateTime());
            $notification->setIsRead(false);

            // Référence à l'hébergement
            $hebergement = $this->getReference('hebergement-' . $i, \App\Entity\Hebergement::class);
            $notification->setHebergement($hebergement);

            $manager->persist($notification);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            HebergementFixtures::class,
        ];
    }
}
