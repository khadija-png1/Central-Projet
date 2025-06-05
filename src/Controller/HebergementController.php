<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hebergement;
use App\Form\SearchType;
use App\Service\HistoriqueService;
use App\Form\HebergementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\HebergementRepository;
use App\Service\HebergementExpirationService;

#[Route('/hebergement')]
final class HebergementController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private HebergementExpirationService $hebergementExpirationService;

    public function __construct(EntityManagerInterface $entityManager, HebergementExpirationService $hebergementExpirationService)
    {
        $this->entityManager = $entityManager;
        $this->hebergementExpirationService = $hebergementExpirationService;
    }

    #[Route('/', name: 'app_hebergement_index')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        // Création du formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        $repository = $doctrine->getRepository(Hebergement::class);
        $hebergements = $repository->findAll();

        // Traitement de la recherche
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!empty($data['search'])) {
                $hebergements = $repository->createQueryBuilder('h')
                    ->where('h.fournisseur LIKE :search')
                    ->setParameter('search', '%' . $data['search'] . '%')
                    ->getQuery()
                    ->getResult();
            }
        }

        return $this->render('hebergement/index.html.twig', [
            'hebergements' => $hebergements,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_hebergement_new', methods: ["GET","POST"])]
    public function new(Request $request): Response
    {
        $hebergement = new Hebergement();
        $form = $this->createForm(HebergementType::class, $hebergement);
        $form->remove('created');
        $form->remove('updated');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hebergement->setCreated(new \DateTime());
            $hebergement->setUpdated(new \DateTime());
            $this->entityManager->persist($hebergement);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_hebergement_index');
        }

        return $this->render('hebergement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'app_hebergement_update', methods: ["GET","POST"])]
    public function update(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager,
        HistoriqueService $historiqueService
     ): Response
    {
        $hebergement = $this->entityManager->getRepository(Hebergement::class)->find($id);
        if (!$hebergement) {
            throw $this->createNotFoundException('Hébergement non trouvé avec l\'id ' . $id);
        }

        $form = $this->createForm(HebergementType::class, $hebergement);
        $form->remove('created');
        $form->remove('updated');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hebergement->setUpdated(new \DateTime());
            $historiqueService->log($hebergement, 'modification');
            $this->entityManager->flush();
            return $this->redirectToRoute('app_hebergement_index');
        }

        return $this->render('hebergement/update.html.twig', [
            'form' => $form->createView(),
            'hebergement' => $hebergement,
        ]);
    }
    #[Route('/delete/{id}', name: 'app_hebergement_delete', methods: ['GET', 'POST'])]
    public function delete(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $hebergement = $entityManager->getRepository(Hebergement::class)->find($id);
    
        if (!$hebergement) {
            throw $this->createNotFoundException('Aucun hebergement trouvé avec l\'id ' . $id);
        }
    
        // Supprimer les notifications liées au projet
        foreach ($hebergement->getNotifications() as $notification) {
            $entityManager->remove($notification);
        }
    
        // Supprimer le projet après avoir supprimé les dépendances
        $entityManager->remove($hebergement);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_hebergement_index');
        
    } 
    #[Route('/show/{id}', name: 'app_hebergement_show', methods: ["GET"])]
    public function show(int $id): Response
    {
        $hebergement = $this->entityManager->getRepository(Hebergement::class)->find($id);
        if (!$hebergement) {
            throw $this->createNotFoundException('Aucun hébergement trouvé avec l\'id ' . $id);
        }

        return $this->render('hebergement/show.html.twig', [
            'hebergement' => $hebergement,
        ]);
    }
}
