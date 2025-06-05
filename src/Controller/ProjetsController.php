<?php

namespace App\Controller;

use App\Entity\Historique;
use App\Entity\Projet;
use App\Form\ProjetType;
use App\Form\SearchType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\UrlCheckerService;
use App\Service\HistoriqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projets')]
class ProjetsController extends AbstractController
{
    #[Route('/', name: 'app_projets_index_main', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, ProjetRepository $projetRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $projets = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!empty($data['search'])) {
                $projets = $projetRepository->createQueryBuilder('p')
                    ->where('p.nom LIKE :search')
                    ->setParameter('search', '%' . $data['search'] . '%')
                    ->getQuery()
                    ->getResult();
            } else {
                $projets = $projetRepository->findAll();
            }
        } else {
            $projets = $projetRepository->findAll();
        }

        return $this->render('projets/index.html.twig', [
            'projets' => $projets,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/new', name: 'app_projets_new', methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->remove('created');
        $form->remove('updated');
        $form->remove('status');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $projet->setCreated(new \DateTime());
            $projet->setUpdated(new \DateTime());
            $entityManager->persist($projet);
            $entityManager->flush();

           

            return $this->redirectToRoute('app_projets_index_main');
        }

        return $this->render('projets/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/show/{id}', name: 'app_projets_show', methods: ['GET'])]
    public function show(Projet $projet, UrlCheckerService $checker): Response
    {
        $url = $projet->getUrl();
    
        if (!$url) {
            $this->addFlash('warning', 'Aucune URL définie pour ce projet.');
            // Tu peux choisir de rendre la vue sans tester l'URL ou rediriger ailleurs
            return $this->render('projets/show.html.twig', [
                'projet' => $projet,
            ]);
        }
    
        $result = $checker->checkUrl($url);
    
        if ($result['success']) {
            $this->addFlash('success', "✅ L'URL {$result['url']} est accessible (statut HTTP {$result['status_code']}).");
        } else {
            $this->addFlash('danger', "❌ L'URL {$result['url']} est inaccessible. Erreur : {$result['error']}");
        }
    
        return $this->render('projets/show.html.twig', [
            'projet' => $projet,
        ]);
    }    
    #[Route('/update/{id}', name: 'app_projets_update', methods: ["GET", "POST"])]
    public function update(
            Request $request, 
            Projet $projets,
            int $id,
            EntityManagerInterface $entityManager,
            HistoriqueService $historiqueService
            ): Response
        {
            $form = $this->createForm(ProjetType::class, $projets);
            $form->remove('created');
            $form->remove('updated');

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $projets->setUpdated(new \DateTime());
                $historiqueService->log($projets, 'modification');
                $entityManager->flush();
                return $this->redirectToRoute('app_projets_index_main');
            }

            return $this->render('projets/update.html.twig', [
                'projets' => $projets,
                'form' => $form->createView(),
            ]);
    }
    #[Route('/delete/{id}', name: 'app_projet_delete', methods: ['GET', 'POST'])]
    public function delete(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $projet = $entityManager->getRepository(Projet::class)->find($id);
    
        if (!$projet) {
            throw $this->createNotFoundException('Aucun projet trouvé avec l\'id ' . $id);
        }
    
        // Supprimer les notifications liées au projet
        foreach ($projet->getNotifications() as $notification) {
            $entityManager->remove($notification);
        }
    
        // Supprimer le projet après avoir supprimé les dépendances
        $entityManager->remove($projet);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_projets_index_main');
        
    } 
    #[Route('/projet/ping/{id}', name: 'projet_ping')]
    public function pingProjet(Projet $projet, UrlCheckerService $checker): Response
    {
        $url = $projet->getUrl(); // ou getUrl()

        if (!$url) {
            return new Response('Aucune URL définie pour ce projet.', 400);
        }

        $result = $checker->checkUrl($url);

        if ($result['success']) {
            return $this->render('projets/ping_success.html.twig', [
                'url' => $url,
                'status_code' => $result['status_code']
            ]);
        } else {
            return $this->render('projets/ping_error.html.twig', [
                'error' => $result['error'],
                'url' => $url
            ]);
        }
    }
    

  
}
