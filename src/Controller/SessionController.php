<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'session')]
    public function index(Request $request): Response
    {
        // equivalent a session_start() en php natif
        $session = $request->getSession();
        if ($session->has('nbVisite')) {
            // si la session existe deja alors faire une incrementation
            $nbreVisite = $session->get('nbVisite') + 1;
        } else {
            // autrement prendre 1
            $nbreVisite = 1;
        }

        $session->set('nbVisite',$nbreVisite);


        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
        ]);
    }
}
