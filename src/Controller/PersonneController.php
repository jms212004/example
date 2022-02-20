<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('personne')]

class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $personnes = $repository->findAll();

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
    
    }

    
    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
    {
        if (!$personne) {
            $this->addFlash('error',"La personne n'existe pas");
            return $this->redirectToRoute('personne.list');
        }

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    
    }

    
    #[Route('/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        //rajouter des informations en BDD
        $personne = new Personne();

        // creation d une personne aléatoirement
        $i = rand(0,200);

        // inialisation des variables
        $firstname = 'firstname'.$i;
        $name = 'name'.$i;
        $age = $i;
        
        $personne->setFirstname($firstname);
        $personne->setName($name);
        $personne->setAge($age);
            
        // ajouter operation insertion de la personne dans la transaction
        $entityManager->persist($personne);
        //execution de la transaction vers la BDD (execute)
        $entityManager->flush();
        

        // affichage des informations dans la page detail
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    }
}
