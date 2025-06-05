<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Projet;
use App\Entity\Developpeur;
use App\Form\SearchType;
use App\Form\ProjetType;
use Symfony\Bundle\SecurityBundle\Security;
use App\Service\HistoriqueService;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projet/devloppeur')] // Typo here
class ProjetDevloppeurController extends AbstractController
{
    #[Route('/', name: 'app_projet_devloppeur', methods: ["GET", "POST"])]
    public function index(
            Request $request,
            ProjetRepository $projetRepository,
            EntityManagerInterface $entityManager,
            Security $security
        ): Response {
            $form = $this->createForm(SearchType::class);
            $form->handleRequest($request);
        
            $projets = [];
        
            $user = $security->getUser();
        
            if ($user) {
                $developpeur = $entityManager->getRepository(Developpeur::class)->findOneBy(['user' => $user]);
        
                if ($developpeur) {
                    // Si recherche
                    if ($form->isSubmitted() && $form->isValid()) {
                        $data = $form->getData();
                        $search = $data['search'] ?? '';
        
                        if (!empty($search)) {
                            $projets = $projetRepository->createQueryBuilder('p')
                                ->innerJoin('p.developpeur', 'd')
                                ->where('d = :dev')
                                ->andWhere('p.nom LIKE :search')
                                ->setParameter('dev', $developpeur)
                                ->setParameter('search', '%' . $search . '%')
                                ->getQuery()
                                ->getResult();
                        } else {
                            $projets = $developpeur->getProjets(); // Retourne une Collection
                        }
                    } else {
                        $projets = $developpeur->getProjets(); // Retourne une Collection
                    }
                }
            }
        
            if ($request->isXmlHttpRequest()) {
                return $this->render('projet_devloppeur/_search_result.html.twig', [
                    'projets' => $projets,
                ]);
            }
        
            return $this->render('projet_devloppeur/index.html.twig', [
                'form' => $form->createView(),
                'projets' => $projets,
            ]);
    }
        

    
    
    #[Route('/update/{id}', name: 'app_projet_devloppeur_update', methods: ["GET", "POST"])]
    public function update(
        Request $request, 
        Projet $projets, 
        int $id , 
        EntityManagerInterface $entityManager ,
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
        }

        return $this->render('projet_devloppeur/update.html.twig', [
            'projets' => $projets,
            'form' => $form->createView(),
        ]);
    }





















#[Route('/show/{id}', name: 'app_projet_devloppeur_show', methods: ["GET"])]
        public function show(ProjetRepository $projetRepository, int $id): Response
        {
            $projet = $projetRepository->find($id);

            if (!$projet) {
                throw $this->createNotFoundException('Aucun projet trouvé avec l\'id ' . $id);
            }

            return $this->render('projet_devloppeur/show.html.twig', [
                'projet' => $projet,
            ]);
        }




}
