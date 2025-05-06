<?php

namespace App\Controller;

use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Projet;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        // Création du formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Initialiser la query builder pour la recherche
        $queryBuilder = $em->getRepository(Projet::class)->createQueryBuilder('p');

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Appliquer les filtres si les champs sont remplis
            if (!empty($data['search'])) {
                $queryBuilder->andWhere('p.nom LIKE :search')
                             ->setParameter('search', '%' . $data['search'] . '%');
            }
        }

        // Exécuter la requête et récupérer les résultats
        $projets = $queryBuilder->getQuery()->getResult();

        // Afficher les résultats et le formulaire
        return $this->render('search/search.html.twig', [
            'form' => $form->createView(),
            'projets' => $projets,
        ]);
    }
}
