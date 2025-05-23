<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
   
final class MonCompteController extends AbstractController
{
    // Route pour la page de compte
    #[Route('/compte', name: 'app_mon_compte', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager  ): Response
     
{ 
      // Récupère l'utilisateur connecté
      $user = $this->getUser();



    $form = $this->createForm(UserType::class, $user);
    $form->remove('password');
    $form->remove('updated');
    $form->remove('created');
    
  
    // Vérifie que l'utilisateur est authentifié
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
    }

    return $this->render('compte/index.html.twig', [
        'user' => $user,
        'form' => $form, 
    ]);
}


   // Route pour la page de compte
   #[Route('/compte/user', name: 'app_compte_user', methods: ['GET'])]
   public function compte_user(EntityManagerInterface $entityManager  ): Response
        
   { 
         // Récupère l'utilisateur connecté
         $user = $this->getUser();
   
   
   
       $form = $this->createForm(UserType::class, $user);
       $form->remove('password');
       $form->remove('updated');
       $form->remove('created');
       
     
       // Vérifie que l'utilisateur est authentifié
       if (!$user) {
           throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
       }
   
       return $this->render('compte/index_user.html.twig', [
           'user' => $user,
           'form' => $form, 
       ]);
   }


    // Route pour la mise à jour du compte
    #[Route('/compte/update/{id}', name: 'app_mon_compte_update', methods: ['GET', 'POST'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->remove('password');
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdated(new \DateTime());
            $entityManager->flush();
            return $this->redirectToRoute('app_mon_compte');
        }

        return $this->render('compte/update_modal.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
