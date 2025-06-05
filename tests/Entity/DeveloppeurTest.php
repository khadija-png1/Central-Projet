<?php

namespace App\Tests\Entity;

use App\Entity\Developpeur;
use App\Entity\Projet;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class DeveloppeurTest extends TestCase
{
  public function testGettersSetters()
  {
    $developpeur = new Developpeur();

    $nom = 'Dupont';
    $prenom = 'Jean';
    $email = 'jean.dupont@example.com';
    $telephone = '0123456789';

    $user = $this->createMock(User::class);

    // Test setters
    $developpeur->setNom($nom);
    $developpeur->setPrenom($prenom);
    $developpeur->setEmail($email);
    $developpeur->setTelephone($telephone);
    $developpeur->setUser($user);

    // Test getters
    $this->assertSame($nom, $developpeur->getNom());
    $this->assertSame($prenom, $developpeur->getPrenom());
    $this->assertSame($email, $developpeur->getEmail());
    $this->assertSame($telephone, $developpeur->getTelephone());
    $this->assertSame($user, $developpeur->getUser());

    // Test __toString
    $this->assertSame('Dupont Jean', (string) $developpeur);
  }

  public function testAddRemoveProjet()
  {
    $developpeur = new Developpeur();
    $projet = $this->createMock(Projet::class);

    // On simule que addDeveloppeur() est appelé sur projet
    $projet->expects($this->once())
      ->method('addDeveloppeur')
      ->with($developpeur);

    $developpeur->addProjet($projet);

    $this->assertCount(1, $developpeur->getProjets());
    $this->assertTrue($developpeur->getProjets()->contains($projet));

    // Simule removeDeveloppeur() appelé sur projet
    $projet->expects($this->once())
      ->method('removeDeveloppeur')
      ->with($developpeur);

    $developpeur->removeProjet($projet);

    $this->assertCount(0, $developpeur->getProjets());
  }
}
