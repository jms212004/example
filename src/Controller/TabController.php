<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//factoriser l uri et limiter acces au role pour tout le controler
#[
    Route('tab'),
    IsGranted("ROLE_ADMIN")]
class TabController extends AbstractController
{
    #[Route('/{nb<\d+>?5}', name: 'tab')]
    public function index($nb): Response
    {
        $notes = [];
        for ($i=0; $i<$nb ; $i++) {
            $notes[] = rand(0,20);
        }
        return $this->render('tab/index.html.twig', [
            'notes' => $notes,
            'path' => 'images2'
        ]);
    }

    #[Route('/users', name: 'tab.users')]
    public function users(): Response
    {
        $users = [
            ['firstname' => 'jm', 'name' => 's', 'age' => '22'],
            ['firstname' => 'jm2', 'name' => 's2', 'age' => '23'],
            ['firstname' => 'jm3', 'name' => 's3', 'age' => '24']
        ];
        return $this->render('tab/users.html.twig', [
            'users' => $users
        ]);
    }


    #[Route('/utilisateurs', name: 'tab.listuser')]
    public function utilisateur(ManagerRegistry $doctrine): Response
    {
        // droit acces uniquement a admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $doctrine->getRepository(User::class);

        $users = $repository->findAll();

        return $this->render('tab/utilisateurs.html.twig', [
            'users' => $users
        ]);
    
    }
}
