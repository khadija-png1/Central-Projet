<?php
namespace App\Controller;
use App\Entity\Projet;
use App\Entity\Developpeur;
use App\Entity\Hebergement;
use App\Entity\Technologie;
use App\Entity\Historique;
use App\Entity\Notification;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\HebergementExpirationService;


class SearchController extends AbstractController
{
    #[Route('/projets', name: 'app_projets_index')]
public function index_projet(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    // Liste des projets par défaut
    $projets = $em->getRepository(Projet::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        if (!empty($data['search'])) {
            // Recherche des projets par nom
            $projets = $em->getRepository(Projet::class)
                ->createQueryBuilder('p')
                ->where('p.nom LIKE :search')
                ->setParameter('search', '%' . $data['search'] . '%')
                ->getQuery()
                ->getResult();

            }
 

        return $this->render('projets/_search_result.html.twig',[
            'form' => $form->createView(),
            'projets' => $projets,
        ]);
 
    }

    return $this->render('projets/index.html.twig', [
        'form' => $form->createView(),  // Assurez-vous de passer cette variable
        'projets' => $projets,
    ]);
}



    #[Route('/developpeur', name: 'app_developpeur_index')]
public function index_developpeur(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    // Liste des développeurs par défaut
    $developpeurs = $em->getRepository(Developpeur::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        if (!empty($data['search'])) {
            // Recherche des développeurs par nom
            $developpeurs = $em->getRepository(Developpeur::class)
                ->createQueryBuilder('d')
                ->where('d.nom LIKE :search')
                ->setParameter('search', '%' . $data['search'] . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('developpeur/_search_result.html.twig', [
            'form' => $form->createView(),
            'developpeurs' => $developpeurs,
        ]);
    }

    return $this->render('developpeur/index.html.twig', [
        'form' => $form->createView(),
        'developpeurs' => $developpeurs,
    ]);
}



    #[Route('/hebergement', name: 'app_hebergement_index')]
public function index_hebergement(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    // Liste des hebergement par défaut
    $hebergements = $em->getRepository(Hebergement::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        if (!empty($data['search'])) {
            // Recherche des hebergement par nom
            $hebergements = $em->getRepository(Hebergement::class)
                ->createQueryBuilder('h')
                ->where('h.fournisseur LIKE :search')
                ->setParameter('search', '%' . $data['search'] . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('hebergement/_search_result.html.twig', [
            'form' => $form->createView(),
            'hebergements' => $hebergements,
        ]);
    }

    return $this->render('hebergement/index.html.twig', [
        'form' => $form->createView(),
        'hebergements' => $hebergements,
    ]);
}


    #[Route('/technologie', name: 'app_index_technologie')]
public function index_technologie(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    // Liste des technologie par défaut
    $technologies = $em->getRepository(Technologie::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        if (!empty($data['search'])) {
            // Recherche des technologie par nom
            $technologies = $em->getRepository(Technologie::class)
                ->createQueryBuilder('t')
                ->where('t.nom LIKE :search')
                ->setParameter('search', '%' . $data['search'] . '%')
                ->getQuery()
                ->getResult();
                
        }
        return $this->render('technologie/_search_result.html.twig', [
            'form' => $form->createView(),
            'technologies' => $technologies,
        ]);
       
    }

    return $this->render('technologie/index.html.twig', [
        'form' => $form->createView(),
        'technologies' => $technologies,
    ]);


}

    #[Route('/historique', name: 'app_historique_index')]
public function index_historique(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    // Liste des historiques par défaut
    $historiques = $em->getRepository(Historique::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        
        if (!empty($data['search'])) {
            $historiques = $em->getRepository(Historique::class)
                ->createQueryBuilder('ht')
                ->where('ht.entiteModifiee LIKE :search')
                ->setParameter('search', '%' . $data['search'] . '%')
                ->getQuery()
                ->getResult();
        }

        // Rendu du template pour les résultats de recherche AJAX
        return $this->render('historique/_search_result.html.twig', [
            'form' => $form->createView(),
            'historiques' => $historiques,
        ]);
    }

    // Rendu de la page complète
    return $this->render('historique/index.html.twig', [
        'form' => $form->createView(),
        'historiques' => $historiques,
    ]);
}

    #[Route('/AlertesNotifications', name: 'app_alertes_notifications')]
    public function index_notification(
        Request $request,
        EntityManagerInterface $em,
        HebergementExpirationService $expirationService // 👈 injection du service
        ): Response
{
    // ✅ Appel du service pour déclencher la vérification

    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    // Liste des notifications par défaut
    $notifications = $em->getRepository(Notification::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        if (!empty($data['search'])) {
            // Recherche des notifications par message
            $notifications = $em->getRepository(Notification::class)
                ->createQueryBuilder('a')
                ->where('a.message LIKE :search')
                ->setParameter('search', '%' . $data['search'] . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('AlertesNotifications/_search_result.html.twig', [
            'form' => $form->createView(),
            'notifications' => $notifications,
        ]);
    }

    return $this->render('AlertesNotifications/index.html.twig', [
        'form' => $form->createView(),
        'notifications' => $notifications,
    ]);
}


#[Route('/projet/devloppeur', name: 'app_projets_developpeur_index')]
public function index_projet_developpeur(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    // Liste des projets par défaut
    $projets = $em->getRepository(Projet::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        if (!empty($data['search'])) {
            // Recherche des projets par nom
            $projets = $em->getRepository(Projet::class)
                ->createQueryBuilder('p')
                ->where('p.nom LIKE :search')
                ->setParameter('search', '%' . $data['search'] . '%')
                ->getQuery()
                ->getResult();

            }
 

        return $this->render('projet_devloppeur/_search_result.html.twig',[
            'form' => $form->createView(),
            'projets' => $projets,
        ]);
 
    }

    return $this->render('projet_devloppeur/index.html.twig', [
        'form' => $form->createView(),  // Assurez-vous de passer cette variable
        'projets' => $projets,
    ]);
}



}