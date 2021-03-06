<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PersonneType;
use App\Service\MailerService;
use App\Service\UploaderService;
use App\Service\PdfService;
use App\Service\HelpersService;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Security;
use Psr\Log\LoggerInterface;

//factoriser l uri et limiter acces au role pour tout le controler
#[
    Route('personne'),
    IsGranted("ROLE_USER")]

class PersonneController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private HelpersService $helpers,
        private EventDispatcherInterface $dispatcher
    )
    {}

    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $personnes = $repository->findAll();

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
    
    }


    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf) {
        $html = $this->render('personne/detail.html.twig', ['personne' => $personne]);
        $pdf->showPdfFile($html);
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


    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine,$page,$nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $arrayCritereFiltre = array();
        $arrayCritereOrderBy = array();
        //exemple de filtre 
        //$arrayCritereFiltre = array('firstname'=>'Honor??');
        //$arrayCritereOrderBy = array('age' => 'ASC');
        
        // droit acces uniquement a admin
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $offset = ($page-1) * $nbre;//commencer au numero ?
        $limit = $nbre;
        $nbreDePersonne = $repository->count($arrayCritereFiltre);
        $nbreDePage = ceil($nbreDePersonne / $nbre);

        

        //afficher uniquement l id 410
        //$personnes = $repository->findBy(['id'=>'410']);
        //zfficher les personnes avec le pr??nom correspondant
        //$personnes = $repository->findBy(['firstname'=>'Honor??']);

        //afficher les personnes avec le prenom correspondant et trier par date
        //$personnes = $repository->findBy(['firstname'=>'Honor??'],['age' => 'ASC']);

        //afficher les personnes avec le prenom correspondant et trier par date et limite ?? 2
        //$personnes = $repository->findBy(['firstname'=>'Honor??'],['age' => 'ASC'],2);

        //afficher les personnes avec le prenom correspondant et trier par date et limite ?? 2 et commencer au deuxieme
        //$personnes = $repository->findBy(['firstname'=>'Honor??'],['age' => 'ASC'],$nbre,2);

        
        $personnes = $repository->findBy($arrayCritereFiltre,$arrayCritereOrderBy,$limit,$offset);
        //events
        $listAllPersonnesEvents = new ListAllPersonnesEvent(count($personnes));
        $this->dispatcher->dispatch($listAllPersonnesEvents, ListAllPersonnesEvent::LIST_ALL_PERSONNE_EVENT);

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
            return $this->redirectToRoute('personne.list.alls');
        }

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    
    }

    /**
     * Methode addPersonne
     * Permet d'??diter ou d ajouter une personne
     */
    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mailer,
        ): Response
    {
        // droit acces uniquement a admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // initialisation du texte du message a afficher
        $message = " a ??t?? mis ?? jour avec succ??s";
        $newPersonne = false;
        // si id retourn?? ne remonter aucun personne de la bDD
        //alors on consid??re la cr??ation d'un personne
        if (!$personne) {
            $newPersonne = true;
            // instancier la classe personne
            $personne = new Personne();
            
        }
        
        // creation des champs du formulaire a partir de la classe personne
        $form = $this->createForm(PersonneType::class, $personne);
        //ne pas afficher certains champs (connu dans la classe personnne)
        $form->remove('createdAt');
        $form->remove('updatedAt');

        // Mon formulaire va  traiter la requete
        $form->handleRequest($request);

        //Est ce que le formulaire a ??t?? soumis et valide
        if($form->isSubmitted() && $form->isValid()) {
            // si oui,
                // traiter le depot d un fichier
                $photo = $form->get('photo')->getData();//recuperation de  la photo
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($photo) {
                    $directory = $this->getParameter('personne_directory');
                    $personne->setImage($uploaderService->uploadFile($photo, $directory));
                }
                if ($newPersonne) {
                    $message = " a ??t?? cr???? avec succ??s";
                    $personne->setCreatedBy($this->getUser());
                    //dd($helpers->getUser);
                }
                
                // on va ajouter l'objet personne dans la base de donn??es
                $manager = $doctrine->getManager();
                $manager->persist($personne);
                $manager->flush();

                // Afficher un mssage de succ??s
                if($newPersonne) {
                    // On a cr??er notre evenenement
                    $addPersonneEvent = new AddPersonneEvent($personne);
                    // On va maintenant dispatcher cet ??v??nement
                    $this->dispatcher->dispatch($addPersonneEvent, AddPersonneEvent::ADD_PERSONNE_EVENT);
                }
                // Afficher un mssage de succ??s
                $this->addFlash('success',$personne->getName(). $message );

                /*
                * Morceau de code d??plac?? dans le EventSubscriber
                //log
                $this->logger->info('success ' .$personne->getName(). $message);

                //envoyer un courriel
                $mailer->sendEmail(content: '<p>See Twig integration for better HTML integration!</p>');
                */
                // Rediriger verts la liste des personne
                return $this->redirectToRoute('personne.list.alls');

            } else {
                //sinon 
            //On affiche notre formulaire

            // affichage des informations dans la page detail
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView()//view
            ]);
        }
    }

    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine):RedirectResponse {
        // droit acces uniquement a admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // R??cup??rer la personne
        if ($personne) {
            // Si la personne existe => le supprimer et retourner un flashMessage de succ??s
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            // Ex??cuter la transacition
            $manager->flush();
            $this->addFlash('success', "La personne a ??t?? supprim?? avec succ??s");
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
            // Si la personne existe => le supprimer et retourner un flashMessage de succ??s
            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            $this->addFlash('success', "La personne a ??t?? mise ?? jour avec succ??s");

        } else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Personne inexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
}
