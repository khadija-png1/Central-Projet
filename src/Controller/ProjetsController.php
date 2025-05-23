<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Form\SearchType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
    public function show(Projet $projet): Response
    {
        return $this->render('projets/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    #[Route('/update/{id}', name: 'app_projets_update', methods: ["GET", "POST"])]
    public function update(Request $request, Projet $projets, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projets);
        $form->remove('created');
        $form->remove('updated');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $projets->setUpdated(new \DateTime());
            $entityManager->flush();
            return $this->redirectToRoute('app_projets_index_main');
        }

        return $this->render('projets/update.html.twig', [
            'projets' => $projets,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_projets_delete', methods: ["POST"])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $projet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projets_index_main');
    }
}
