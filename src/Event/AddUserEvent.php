<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;


class AddUserEvent extends Event
{
    const ADD_USER_EVENT = 'user.add';

    public function __construct(private User $user) {}

    public function getUser(): User {
        return $this->user;
    }

}