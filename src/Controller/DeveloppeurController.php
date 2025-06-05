<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Developpeur;
use App\Form\DeveloppeurType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;




#[Route('/developpeur')]
final class DeveloppeurController extends AbstractController
{
    #[Route('/', name: 'app_developpeur_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Developpeur::class);
        $developpeur= $repository->findAll();

        return $this->render('developpeur/index.html.twig', [
            'developpeurs' => $developpeur,
        ]);
    }
    
    #[Route('/new', name: 'app_developpeur_new', methods: ["GET","POST"])]
    public function new(ManagerRegistry $doctrine ,Request $request): Response
    {
                        $developpeur=new Developpeur();
                        $form=$this->createForm(DeveloppeurType::class ,$developpeur);
                        $form->remove('created');
                        $form->remove('updated');
                        $developpeur->setCreated(new \DateTime());
                        $developpeur->setUpdated(new \DateTime());
                        $form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
                        $entityManager=$doctrine->getManager();
                        $entityManager->persist($developpeur);
                        $entityManager->flush();
         return $this->redirectToRoute('app_developpeur_index');

}
        return $this->render('developpeur/new.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }




    #[Route('/update/{id}', name: 'app_developpeur_update', methods: ["GET","POST"])]
    public function update(EntityManagerInterface $entityManager ,ManagerRegistry $doctrine ,Request $request,int $id): Response
    {
        $developpeur = $entityManager->getRepository(Developpeur::class)->find($id);
                      if(!$developpeur)
                    {
                        throw $this->createNotFoundException(
                            'No product found for id '.$id);
                        }
                        $form=$this->createForm(DeveloppeurType::class ,$developpeur);
                        $form->remove('created');
                        $form->remove('updated');
                    
                        $form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()){
                        $developpeur->setUpdated(new \DateTime());
                        $entityManager->flush();

         return $this->redirectToRoute('app_developpeur_index');

}



        return $this->render('developpeur/update.html.twig', [
            'form' => $form->createView(),
            'developpeur' => $developpeur,  // Assurez-vous de passer l'entité devloppeur à Twig

        ]);
    }

    #[Route('/delete/{id}', name: 'app_developpeur_delete', methods: ["GET","POST"])]
    public function delete(ManagerRegistry $doctrine ,int $id): Response
 {
                        $entityManager=$doctrine->getManager();
                        $entityManager = $doctrine->getManager();
    $developpeur = $entityManager->getRepository(Developpeur::class)->find($id);

    if (!$developpeur) {
        throw $this->createNotFoundException('Aucun développeur trouvé avec l\'id '.$id);
    }



                        $entityManager->remove($developpeur);
                        $entityManager->flush();

                        return $this->redirectToRoute('app_developpeur_index');
                    
                    
                    
    }

 }                    
