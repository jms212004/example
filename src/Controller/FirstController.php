<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/first', name: 'first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig', [
            'controller_name' => 'FirstController',
            'firstname' => 'zezez',
            'lastname' => 'jjjj'
        ]);
    }

    #[Route('/sayHello', name: 'say.hello')]
    public function sayHello(): Response
    {
        $rand = rand(0,10);
        $value = 0;
        if ( $rand % 2) {
            return $this->redirectToRoute('first');
        }
        return $this->render('first/hello.html.twig', [
            'controller_name' => 'FirstController',
            'firstname' => 'zezez',
            'lastname' => 'jjjj',
            'random' => $value
        ]);
    }
}
