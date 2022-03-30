<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Event\AddUserEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


//factoriser l uri et limiter acces au role pour tout le controler
#[
    Route('tab'),
    IsGranted("ROLE_ADMIN")]
class TabController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    )
    {}

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

    #[Route('/edit/{id?0}', name: 'tab.edit')]
    public function addUsers(
        User $user = null,
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $userPasswordHasher,
        Request $request
    ): Response
    {
        // droit acces uniquement a admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // initialisation du texte du message a afficher
        $message = " a été mis à jour avec succès";
        $newUser = false;
        // si id retourné ne remonter aucun user de la bDD
        //alors on considère la création d'un user
        if (!$user) {
            $newUser = true;
            // instancier la classe user
            $user = new User();
        }


        // creation des champs du formulaire a partir de la classe personne
        $form = $this->createForm(UserType::class, $user);
        
        // Mon formulaire va  traiter la requete
        $form->handleRequest($request);

        //Est ce que le formulaire a été soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                //mise à jour de date update et create
                $now = new DateTimeImmutable();
                $user->setUpdatedAt($now);
        
                if ($newUser) {
                    $message = " a été créé avec succès";
                    $user->setCreatedAt($now);
                }

                // affecter le role
                $user->setRoles(['ROLE_USER']);

            
            // on va ajouter l'objet personne dans la base de données
            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();

            // Afficher un mssage de succès
            if($newUser) {
                // On a créer notre evenenement
                $addUserEvent = new AddUserEvent($user);
                // On va maintenant dispatcher cet événement
                $this->dispatcher->dispatch($addUserEvent, AddUserEvent::ADD_USER_EVENT);
            }

            // Afficher un mssage de succès
            $this->addFlash('success',$user->getName(). $message );


            // Rediriger verts la liste des utilisateurs
            return $this->redirectToRoute('tab.listuser');
        }


        // affichage des informations dans la page detail
        return $this->render('tab/add-utilisateurs.html.twig', [
            'formuser' => $form->createView()//view
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
