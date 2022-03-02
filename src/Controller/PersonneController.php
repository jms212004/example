<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//factoriser l uri
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

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personnesByAge(ManagerRegistry $doctrine,$ageMax,$ageMin): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $stats = $repository->statsPersonnesByAgeInterval($ageMin,$ageMax);

        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin'=> $ageMin,
            'ageMax' => $ageMax
        ]);
    
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.stats')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin'=> $ageMin,
            'ageMax' => $ageMax]
        );
    }


    #[Route('/alls/{page<\d+>?1}/{nbre<\d+>?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine,$page,$nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $arrayCritereFiltre = array();
        $arrayCritereOrderBy = array();
        //exemple de filtre 
        //$arrayCritereFiltre = array('firstname'=>'Honoré');
        //$arrayCritereOrderBy = array('age' => 'ASC');

        $offset = ($page-1) * $nbre;//commencer au numero ?
        $limit = $nbre;
        $nbreDePersonne = $repository->count($arrayCritereFiltre);
        $nbreDePage = ceil($nbreDePersonne / $nbre);

        //afficher uniquement l id 410
        //$personnes = $repository->findBy(['id'=>'410']);
        //zfficher les personnes avec le prénom correspondant
        //$personnes = $repository->findBy(['firstname'=>'Honoré']);

        //afficher les personnes avec le prenom correspondant et trier par date
        //$personnes = $repository->findBy(['firstname'=>'Honoré'],['age' => 'ASC']);

        //afficher les personnes avec le prenom correspondant et trier par date et limite à 2
        //$personnes = $repository->findBy(['firstname'=>'Honoré'],['age' => 'ASC'],2);

        //afficher les personnes avec le prenom correspondant et trier par date et limite à 2 et commencer au deuxieme
        //$personnes = $repository->findBy(['firstname'=>'Honoré'],['age' => 'ASC'],$nbre,2);

        
        $personnes = $repository->findBy($arrayCritereFiltre,$arrayCritereOrderBy,$limit,$offset);


        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => TRUE,//faire apparaitre la pagination
            'nbrePage' => $nbreDePage,
            'page' => $page,
            'nbre' => $nbre
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

    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine):RedirectResponse {
        // Récupérer la personne
        if ($personne) {
            // Si la personne existe => le supprimer et retourner un flashMessage de succés
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            // Exécuter la transacition
            $manager->flush();
            $this->addFlash('success', "La personne a été supprimé avec succès");
        } else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Personne inexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }


    #[route('/update/{id}/{name}/{firstname}/{age}',name:'personne.update')]
    public function updatePersonne(Personne $personne = null,ManagerRegistry $doctrine,$name,$firstname,$age) {
        // verifier que la personne existe
        if ($personne) {
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            // Si la personne existe => le supprimer et retourner un flashMessage de succés
            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            $this->addFlash('success', "La personne a été mise à jour avec succès");

        } else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Personne inexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
}
