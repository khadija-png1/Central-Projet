<?php

namespace App\Tests\Entity;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ResetPasswordRequestTest extends TestCase
{
    public function testResetPasswordRequestEntity()
    {
        // Crée un mock de User (ou une vraie instance si tu préfères)
        $user = $this->createMock(User::class);

        $expiresAt = new \DateTime('+1 hour');
        $selector = 'selector123';
        $hashedToken = 'hashedtoken123';

        // Instancie ta classe ResetPasswordRequest
        $resetRequest = new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);

        // Teste les méthodes issues du trait
        $this->assertSame($selector, $resetRequest->getSelector());
        $this->assertSame($hashedToken, $resetRequest->getHashedToken());
        $this->assertEquals($expiresAt, $resetRequest->getExpiresAt());

        // Teste la méthode getUser()
        $this->assertSame($user, $resetRequest->getUser());
    }
}
