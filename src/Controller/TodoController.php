<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/todo")]

class TodoController extends AbstractController
{
    private $nameSession = 'todos';

    #[Route('', name: 'todo')]
    public function index(Request $request): Response
    {
        // session
        $session = $request->getSession();
        // Afficher notre tableau de todo
        //si initialisation
        if (!$session->has($this->nameSession)) {
            $todos = [
                'achat'=> 'acheter clé usb',
                'cours' => 'Finalisation du cours',
                'correction' => 'correction mes examens'
            ];
            
            //placer ce tableau en session 
            $session->set($this->nameSession,$todos);
            // afficher message temporaire
            $this->addFlash('info',"La liste des todos vient d' être inialisée");
        }
        

        // else mon tableau de todo dans ma session que je vais afficher
        return $this->render('todo/index.html.twig');
    }

    #[Route(
        '/add/{name}/{content}', 
        name: 'todo.add',
        defaults: ['name'=> 'nameDefaut','content'=> 'Valeur par défaut à prendre']
        )]
    public function addTodo(Request $request,$name, $content):RedirectResponse
    {
        $session = $request->getSession();
        // verifier si il y a un tableau de todo en session
        if ($session->has($this->nameSession)) {
            //verifier si on a deja un todo du meme nom
            $todos = $session->get($this->nameSession);
            if (isset($todos[$name])) {
                //message car todo existe deja
                $this->addFlash('error',"Le todo $name existe déjà");
            } else {
                //rajouter dans le todo
                $todos[$name] = $content;
                $session->set($this->nameSession,$todos);
                $this->addFlash('success',"Le todo $name a été rajouté");
            }
        } else {
            // afficher une erreur et faire une redirection vers le controleur index
            $this->addFlash('error',"La liste n'est pas encore inialisée");
        }

        return $this->redirectToRoute('todo');
    }


    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request,$name, $content):RedirectResponse
    {
        $session = $request->getSession();
        // verifier si il y a un tableau de todo en session
        if ($session->has($this->nameSession)) {
            //verifier si on a deja un todo du meme nom ou pas
            $todos = $session->get($this->nameSession);
            if (!isset($todos[$name])) {
                //message car todo n existe pas
                $this->addFlash('error',"Le todo $name n existe déjà dans la liste");
            } else {
                //rajouter dans le todo
                $todos[$name] = $content;
                $session->set($this->nameSession,$todos);
                $this->addFlash('success',"Le todo $name a été modifié avec succès");
            }
        } else {
            // afficher une erreur et faire une redirection vers le controleur index
            $this->addFlash('error',"La liste n'est pas encore inialisée");
        }

        return $this->redirectToRoute('todo');
    }


    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo(Request $request,$name) :RedirectResponse
    {
        $session = $request->getSession();
        // verifier si il y a un tableau de todo en session
        if ($session->has($this->nameSession)) {
            //verifier si on a deja un todo du meme nom ou pas
            $todos = $session->get($this->nameSession);
            if (!isset($todos[$name])) {
                //message car todo n existe pas
                $this->addFlash('error',"Le todo $name n existe déjà dans la liste");
            } else {
                //rajouter dans le todo
                unset($todos[$name]);
                $session->set($this->nameSession,$todos);
                $this->addFlash('success',"Le todo $name a été supprimé avec succès");
            }
        } else {
            // afficher une erreur et faire une redirection vers le controleur index
            $this->addFlash('error',"La liste n'est pas encore inialisée");
        }

        return $this->redirectToRoute('todo');
    }

    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo(Request $request):RedirectResponse
    {
        $session = $request->getSession();
        
        $session->remove($this->nameSession);
        $this->addFlash('success',"Le todo $this->nameSession a été supprimé avec succès");
        

        return $this->redirectToRoute('todo');
    }

}
