<?php

namespace App\Tests\Entity;

use App\Entity\Hebergement;
use App\Entity\Notification;
use PHPUnit\Framework\TestCase;

class HebergementTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $hebergement = new Hebergement();
        $fournisseur = 'OVH';
        $type = 'VPS';
        $url = 'https://example.com';
        $dateExpiration = new \DateTime('2025-12-31');

        $hebergement->setFournisseur($fournisseur);
        $hebergement->setType($type);
        $hebergement->setUrl($url);
        $hebergement->setDateExpiration($dateExpiration);

        $this->assertSame($fournisseur, $hebergement->getFournisseur());
        $this->assertSame($type, $hebergement->getType());
        $this->assertSame($url, $hebergement->getUrl());
        $this->assertSame($dateExpiration, $hebergement->getDateExpiration());
    }

    public function testAddRemoveProjet()
    {
        $hebergement = new Hebergement();
        $projet = $this->createMock(\App\Entity\Projet::class);

        $projet->expects($this->once())
               ->method('addHebergement')
               ->with($hebergement);

        $hebergement->addProjet($projet);
        $this->assertTrue($hebergement->getProjets()->contains($projet));

        $projet->expects($this->once())
               ->method('removeHebergement')
               ->with($hebergement);

        $hebergement->removeProjet($projet);
        $this->assertFalse($hebergement->getProjets()->contains($projet));
    }

    public function testAddRemoveNotification()
    {
        $hebergement = new Hebergement();
        $notification = $this->createMock(Notification::class);
    
        // Le mock doit accepter deux appels consécutifs avec des paramètres différents
        $notification->expects($this->exactly(2))
                     ->method('setHebergement')
                     ->withConsecutive(
                         [$hebergement], // premier appel : addNotification
                         [null]          // deuxième appel : removeNotification
                     );
    
        $notification->method('getHebergement')
                     ->willReturn($hebergement);
    
        $hebergement->addNotification($notification);
        $this->assertCount(1, $hebergement->getNotifications());
    
        $hebergement->removeNotification($notification);
        $this->assertCount(0, $hebergement->getNotifications());
    }
    
    
}
