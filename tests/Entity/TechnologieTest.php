<?php

namespace App\Tests\Entity;

use App\Entity\Projet;
use App\Entity\Technologie;
use PHPUnit\Framework\TestCase;

class TechnologieTest extends TestCase
{
    public function testTechnologieEntity(): void
    {
        $technologie = new Technologie();

        // Test initial id null
        $this->assertNull($technologie->getId());

        // Test getter/setter nom
        $nom = 'Symfony';
        $technologie->setNom($nom);
        $this->assertSame($nom, $technologie->getNom());

        // Test getter/setter version
        $version = '6.3';
        $technologie->setVersion($version);
        $this->assertSame($version, $technologie->getVersion());

        // Test initial projets collection is empty
        $this->assertCount(0, $technologie->getProjets());

        // Test addProjet et removeProjet
        $projet = $this->createMock(Projet::class);
        // On s'attend à ce que addTechnologie soit appelé sur le projet
        $projet->expects($this->once())
               ->method('addTechnologie')
               ->with($technologie);

        // idem pour removeTechnologie
        $projet->expects($this->once())
               ->method('removeTechnologie')
               ->with($technologie);

        // Add projet
        $technologie->addProjet($projet);
        $this->assertCount(1, $technologie->getProjets());
        $this->assertTrue($technologie->getProjets()->contains($projet));

        // Remove projet
        $technologie->removeProjet($projet);
        $this->assertCount(0, $technologie->getProjets());
        $this->assertFalse($technologie->getProjets()->contains($projet));
    }
}
