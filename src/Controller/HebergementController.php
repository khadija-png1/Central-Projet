<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hebergement;
use App\Form\HebergementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\HebergementRepository;
use App\Service\NotificationService;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\EmailRecipient;
use App\Service\HebergementExpirationService;

#[Route('/hebergement')]

final class HebergementController extends AbstractController
{
    private HebergementExpirationService $expirationService;
    private NotificationService $notificationService;

    public function __construct(HebergementExpirationService $expirationService , NotificationService $notificationService)
    {
        $this->expirationService = $expirationService;
        $this->notificationService = $notificationService;
        
    }
    #[Route('/check-expiration', name: 'check_expiration')]
    public function checkExpirations(): Response
    {
        $this->expirationService->checkExpirationsAndNotify();
        return new Response('Vérification des expirations terminée.');
    }
    

   

  
    
 // ...

    #[Route('/', name: 'app_hebergement_index')]
    public function index(ManagerRegistry $doctrine ): Response
    {
                $repository = $doctrine->getRepository(Hebergement::class);
                $hebergement = $repository->findAll();

        return $this->render('hebergement/index.html.twig', [
            'hebergements' => $hebergement
        ]);
    }



    #[Route('/new', name: 'app_hebergement_new', methods: ["GET","POST"])]
    public function new(ManagerRegistry $doctrine ,Request $request  ): Response
    {
                $hebergement=new Hebergement();
                $form=$this->createForm(HebergementType::class , $hebergement);
                $form->remove('created');
                $form->remove('updated');
                $hebergement->setCreated(new \DateTime());
                $hebergement->setUpdated(new \DateTime());
                $form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
                $entityManager=$doctrine->getManager();
                $entityManager->persist($hebergement);
                $entityManager->flush();


                
            

return $this->redirectToRoute('app_hebergement_index');

}

        return $this->render('hebergement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }






    #[Route('/update/{id}', name: 'app_hebergement_update', methods: ["GET","POST"])]
    public function update(ManagerRegistry $doctrine , Request $request ,EntityManagerInterface $entityManager ,int $id): Response
   {            

    $hebergement=$entityManager->getRepository(Hebergement::class)->find($id);
                if(!$hebergement)
                {
                    throw $this->createNotFoundException('Hebergement not found'.$id);
                }
                $form=$this->createForm(HebergementType::class , $hebergement);
                $form->remove('created');
                $form->remove('updated');
                $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
                $hebergement->setUpdated(new \DateTime());
                $entityManager->flush();
        return $this->redirectToRoute('app_hebergement_index');
        }

        return $this->render('hebergement/update.html.twig', [
                'form' => $form->createView(),
                'hebergement' => $hebergement,
                ]);
            }




    #[Route('/delete/{id}', name: 'app_hebergement_delete', methods: ["GET","POST"])]
    public function delete(HebergementRepository $repository  , EntityManagerInterface $entityManager ,int $id  ): Response
    {
                             $hebergement = $repository->find($id);

        if (!$hebergement) {
                            throw $this->createNotFoundException('Aucun hebergement trouvé avec l\'id '.$id);
                        }
                            $entityManager->remove($hebergement);
                            $entityManager->flush();
                        
                    

        return $this->render('hebergement/delete.html.twig', [
            ' hebergement' => $hebergement
        ]);





         
    }
    
 
}
