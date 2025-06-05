<?php

namespace App\Tests\Entity;

use App\Entity\Historique;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class HistoriqueTest extends TestCase
{
    public function testHistoriqueEntity()
    {
        $historique = new Historique();
        $user = $this->createMock(User::class);
        $date = new \DateTimeImmutable('2024-01-01 12:00:00');

        $historique->setDateModification($date);
        $historique->setEntiteModifiee('Projet');
        $historique->setAction('modification');
        $historique->setUser($user);

        $this->assertSame($date, $historique->getDateModification());
        $this->assertSame('Projet', $historique->getEntiteModifiee());
        $this->assertSame('modification', $historique->getAction());
        $this->assertSame($user, $historique->getUser());
    }
}
