<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    #[Route('/personne/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        //rajouter des informations en BDD
        $personne = new Personne();
        $personne->setFirstname('JM');
        $personne->setName('JMS');
        $personne->setAge('50');


        // ajouter operation insertion de la personne dans la transaction
        $entityManager->persist($personne);

        //execution de la transaction
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    }
}
