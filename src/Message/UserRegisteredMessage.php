<?php

namespace App\Message;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRegisteredMessage
 * @package App\Message
 * @author Frederik Roettgerkamp <frederik@roettgerkamp.com>
 */
class UserRegisteredMessage {

    /**
     * @var UserInterface
     */
    private UserInterface $user;

    /**
     * UserRegisteredMessage constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
