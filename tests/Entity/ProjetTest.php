<?php

namespace App\Tests\Entity;

use App\Entity\Projet;
use App\Entity\Developpeur;
use App\Entity\Hebergement;
use App\Entity\Notification;
use App\Entity\Technologie;
use PHPUnit\Framework\TestCase;

class ProjetTest extends TestCase
{
    public function testProjetEntity()
    {
        $projet = new Projet();
        $projet->setNom('Projet A')
               ->setType('Web')
               ->setDescription('Description...')
               ->setAccesCodeSource('https://github.com/user/projet')
               ->setAccesEnvironnement('https://prod.example.com')
               ->setStatus('en cours')
               ->setUrl('https://example.com');

        $this->assertSame('Projet A', $projet->getNom());
        $this->assertSame('Web', $projet->getType());
        $this->assertSame('Description...', $projet->getDescription());
        $this->assertSame('https://github.com/user/projet', $projet->getAccesCodeSource());
        $this->assertSame('https://prod.example.com', $projet->getAccesEnvironnement());
        $this->assertSame('en cours', $projet->getStatus());
        $this->assertSame('https://example.com', $projet->getUrl());
    }

    public function testProjetCollections()
    {
        $projet = new Projet();

        $tech = $this->createMock(Technologie::class);
        $dev = $this->createMock(Developpeur::class);
        $heb = $this->createMock(Hebergement::class);
        $notif = new Notification();

        $projet->addTechnologie($tech);
        $projet->addDeveloppeur($dev);
        $projet->addHebergement($heb);
        $projet->addNotification($notif);

        $this->assertTrue($projet->getTechnologie()->contains($tech));
        $this->assertTrue($projet->getDeveloppeur()->contains($dev));
        $this->assertTrue($projet->getHebergement()->contains($heb));
        $this->assertTrue($projet->getNotifications()->contains($notif));

        // Vérifie que l'entité Notification connaît son projet
        $this->assertSame($projet, $notif->getProjet());

        // Test du retrait
        $projet->removeTechnologie($tech);
        $projet->removeDeveloppeur($dev);
        $projet->removeHebergement($heb);
        $projet->removeNotification($notif);

        $this->assertFalse($projet->getTechnologie()->contains($tech));
        $this->assertFalse($projet->getDeveloppeur()->contains($dev));
        $this->assertFalse($projet->getHebergement()->contains($heb));
        $this->assertFalse($projet->getNotifications()->contains($notif));
        $this->assertNull($notif->getProjet());
    }
}
