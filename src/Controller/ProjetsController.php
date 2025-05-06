<?php

namespace App\Controller;
use App\Entity\Projet;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProjetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetRepository;

#[Route('/projets')]
final class ProjetsController extends AbstractController
{
    #[Route('/', name: 'app_projets_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
                   $repository = $doctrine->getRepository(Projet::class);
                   $projets = $repository->findAll();

        return $this->render('projets/index.html.twig', [
            'projets' =>$projets,
        ]);
    }

    #[Route('/new', name: 'app_projets_new', methods: ["GET","POST"])]
    public function new(ManagerRegistry $doctrine ,Request $request): Response
    {
                    $projet=new Projet();
                    $form=$this->createForm(ProjetType::class ,$projet);
                    $form->remove('created');
                    $form->remove('updated');
                    $projet->setCreated(new \DateTime());
                    $projet->setUpdated(new \DateTime());
                    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
                    $entityManager=$doctrine->getManager();
                    $entityManager->persist($projet);
                    $entityManager->flush();
    return $this->redirectToRoute('app_projets_index');
    
    }

        return $this->render('projets/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
 



   
    #[Route('/update/{id}', name: 'app_projets_update', methods: ["GET","POST"])]
    public function update(EntityManagerInterface $entityManager ,int $id ,ManagerRegistry $doctrine ,Request $request): Response
    {

        $projet = $entityManager->getRepository(Projet::class)->find($id);
if(!$projet)
        {
            throw $this->createNotFoundException(
                'No product found for id '.$id);
            }
                    
                    $form=$this->createForm(ProjetType::class ,$projet);
                    $form->remove('created');
                    $form->remove('updated');

                    $form->handleRequest($request);
 if($form->isSubmitted() && $form->isValid()){
                                            $projet->setUpdated(new \DateTime());
                                            $entityManager->flush();
                    
                             return $this->redirectToRoute('app_projets_index');
                    
                    }
                    
        return $this->render('projets/update.html.twig', [
            'form' => $form->createView(),
            'projets' => $projet,
        ]);
    }
    
    
    
    #[Route('/delete/{id}', name: 'app_projets_delete', methods: ["GET","POST"])]
    public function delete(ManagerRegistry $doctrine ,ProjetRepository $repository , int $id , EntityManagerInterface $entityManager): Response
    {
                    
                    $projet = $repository->find($id);
                  
    if (!$projet) {
        throw $this->createNotFoundException('Aucun Projet trouvé avec l\'id '.$id);
    }
                        $entityManager->remove($projet);
                        $entityManager->flush();
 
   
        return $this->render('projets/delete.html.twig', [
            'projets' => $projet,
        ]);
    }
}
