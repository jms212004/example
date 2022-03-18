<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class HelpersService
{
    private $langue;
    public function __construct(private LoggerInterface $logger, private Security $security) {
    }
    public function sayCc(): string {
        $this->logger->info('Je dis cc');
        return 'cc';
    }

    public function getUser(): User {
            return $this->security->getUser();
    }
}