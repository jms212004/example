<?php

namespace App\EventSubscriber;

use App\Event\AddUserEvent;
use App\Service\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerService $mailer,
        private LoggerInterface $logger
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            AddUserEvent::ADD_USER_EVENT => ['onAddUserEvent', 3000]
        ];
    }

    public function onAddUserEvent(AddUserEvent $event) {
        $user = $event->getUser();
        $mailMessage = $user->getEmail().' '.$user->getName()." a été ajouté avec succès";
        $this->logger->info("Envoi d'email pour ".$user->getEmail().' '.$user->getName());
        $this->mailer->sendEmail(content: $mailMessage, subject: 'Mail sent from EventSubscriber');
    
    }
}