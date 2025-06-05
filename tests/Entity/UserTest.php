<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity(): void
    {
        $user = new User();

        // Test initial ID is null
        $this->assertNull($user->getId());

        // Test email getter/setter
        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($email, $user->getUserIdentifier());

        // Test roles getter/setter
        $roles = ['ROLE_ADMIN', 'ROLE_MANAGER'];
        $user->setRoles($roles);
        $expectedRoles = array_unique(array_merge($roles, ['ROLE_USER']));
        $this->assertSame($expectedRoles, $user->getRoles());

        // Test password getter/setter
        $password = 'hashed_password';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());

        // Test statut getter/setter
        $statut = 'inactif';
        $user->setStatut($statut);
        $this->assertSame($statut, $user->getStatut());

        // Test nom getter/setter
        $nom = 'Doe';
        $user->setNom($nom);
        $this->assertSame($nom, $user->getNom());

        // Test prenom getter/setter
        $prenom = 'John';
        $user->setPrenom($prenom);
        $this->assertSame($prenom, $user->getPrenom());

        // Test eraseCredentials (just call, no return)
        $user->eraseCredentials();
        $this->assertTrue(true); // rien à tester ici, juste que la méthode ne plante pas
    }
}
