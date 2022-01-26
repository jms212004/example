<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo')]
    public function index(Request $request): Response
    {
        // session
        $session = $request->getSession();
        // Afficher notre tableau de todo
        //si initialisation
        if (!$session->has('todos')) {
            $todos = [
                'achat'=> 'acheter clé usb',
                'cours' => 'Finalisation du cours',
                'correction' => 'correction mes examens'
            ];
            
            //placer ce tableau en session 
            $session->set('todos',$todos);
        }
        
        // afficher message temporaire
        $this->addFlash('info',"La liste des todos vient d' être inialisée");

        // else mon tableau de todo dans ma session que je vais afficher
        return $this->render('todo/index.html.twig');
    }

    #[Route('/todo/add/{name}/{content}', name: 'todo.add')]
    public function addTodo(Request $request,$name, $content)
    {
        $session = $request->getSession();
        // verifier si il y a un tableau de todo en session
        if ($session->has('todos')) {
            //verifier si on a deja un todo du meme nom
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                //message car todo existe deja
                $this->addFlash('error',"Le todo $name existe déjà");
            } else {
                //rajouter dans le todo
                $todos[$name] = $content;
                $session->set('todos',$todos);
                $this->addFlash('success',"Le todo $name a été rajouté");
            }
        } else {
            // afficher une erreur et faire une redirection vers le controleur index
            $this->addFlash('error',"La liste n'est pas encore inialisée");
        }

        return $this->redirectToRoute('todo');

    }
}
