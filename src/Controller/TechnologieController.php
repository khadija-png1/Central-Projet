<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Technologie;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;
use App\Form\TechnologieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TechnologieRepository;
#[Route('/technologie')]
final class TechnologieController extends AbstractController
{
    #[Route('/', name: 'app_technologie_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Technologie::class);
        $technolgie = $repository->findAll();

        return $this->render('technologie/index.html.twig', [
            'technolgies' => $technolgie,
        ]);
    }

    #[Route('/new', name: 'app_technologie_new' ,methods: ["GET","POST"])]
    public function new(ManagerRegistry $doctrine ,Request $request ): Response
    {
        $technologie=new Technologie();
        $form = $this->createForm(TechnologieType::class , $technologie) ;
        $form->remove('created');
        $form->remove('updated');

        $technologie->setCreated(new \DateTime());
        $technologie->setUpdated(new \DateTime());
        
        $form->handleRequest($request);

if($form->isSubmitted() && $form->isValid()){
        $entityManager=$doctrine->getManager();
        $entityManager->persist($technologie);
        $entityManager->flush();
return $this->redirectToRoute('app_technologie_index');

}


        return $this->render('technologie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'app_technologie_update',methods: ["GET","POST"])]
    public function update(ManagerRegistry $doctrine,Request $request , int $id ,EntityManagerInterface $entityManager): Response
    {
        $technologie=$entityManager->getRepository(Technologie::class)->find($id);

if(!$technologie)
        {
            throw $this->createNotFoundException('Technologie not found'.$id);
        }
        $form = $this->createForm(TechnologieType::class , $technologie);
        $form->remove('created');
        $form->remove('updated');
        $form->handleRequest($request);

if($form->isSubmitted() && $form->isValid()){
        $entityManager=$doctrine->getManager();
        $technologie->setUpdated(new \DateTime());
        $entityManager->flush();
    return $this->redirectToRoute('app_technologie_index');
}
        return $this->render('technologie/update.html.twig', [
            'form' => $form->createView(),
            'technologies'=> $technologie,
        ]);
    }




    #[Route('/delete/{id}', name: 'app_technologie_delete',methods: ["GET","POST"])]
    public function delete(TechnologieRepository $repository,  int $id , EntityManagerInterface $entityManager ): Response
    {
    
        $technologie = $repository->find($id);  
 if(!$technologie){
            throw $this->createNotFoundException('Technologie not found'.$id);
        
 }


        $entityManager->remove($technologie);
        $entityManager->flush();

        return $this->render('technologie/delete.html.twig', [
            'technologies' => $technologie,
        ]);
    }
}
