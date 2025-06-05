<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DeveloppeurControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $userRepository = $client->getContainer()->get(UserRepository::class);

        // Chercher un utilisateur existant
        $user = $userRepository->findOneByEmail('user@example.com');

        // Si l'utilisateur n'existe pas, le créer
        if (!$user) {
            $user = new User();
            $user->setEmail('user@example.com');
            $user->setCreated(new \DateTime());

            // Assure-toi de remplir tous les champs obligatoires
            // Exemple : mot de passe, roles, etc.
            $user->setPassword('testpassword'); // normalement hashé, ici juste exemple
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        // Connecter l'utilisateur
        $client->loginUser($user);

        // Faire la requête vers la page à tester
        $client->request('GET', '/developpeur/');

        // Vérifier que la réponse HTTP est 200 OK
        $this->assertResponseIsSuccessful();

        // Autres assertions selon ce que la page doit contenir
        $this->assertSelectorTextContains('h1', 'Liste des développeurs'); // exemple
    }
}
