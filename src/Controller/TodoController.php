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
        if ($session->has('todos')) {
            $todos = [
                'achat'=> 'acheter clé usb',
                'cours' => 'Finalisation du cours',
                'correction' => 'correction mes examens'
            ];
        }
        //placer ce tableau en session 
        $session->set('todos',$todos);

        // else mon tableau de todo dans ma session que je vais afficher
        return $this->render('todo/index.html.twig');
    }
}
