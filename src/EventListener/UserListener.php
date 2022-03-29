<?php

namespace App\EventListener;

use App\Event\AddUserEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class UserListener
{
    public function __construct(private LoggerInterface $logger) {
}
    public function onUserAdd(AddUserEvent $event){
        //dd("cc je suis entrain d'écouter l'evenement User.add et une User vient d'être ajoutée et c'est ". $event->getUser()->getName());
        $this->logger->debug("Events je suis entrain d'écouter l'evenement User.add et une User vient d'être ajoutée et c'est ". $event->getUser());
    }
    /*public function onListAllPersonnes(ListAllPersonnesEvent $event){
        $this->logger->debug("Le nombre de personne dans la base est ". $event->getNbPersonne());
    }
    public function onListAllPersonnes2(ListAllPersonnesEvent $event){
        $this->logger->debug("Le second Listener avec le nbre :". $event->getNbPersonne());
    }*/

    public function logKernelRequest(KernelEvent $event){
        dd($event->getRequest());
    }
}