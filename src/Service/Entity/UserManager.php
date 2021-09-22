<?php

namespace App\Service\Entity;

use App\Entity\User;
use App\Util\EntityMapper;

class UserManager extends BaseManager {

    protected string $repoName = 'App:User';

    /**
     * @var array
     */
    protected array $validation = [
        'username',
        'email',
        'plainPassword',
    ];

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return User::class;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function isUsernameAvailable(string $username): bool
    {
        return $this->repository->findOneBy(['username' => $username]) === null;
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function getByConfirmationToken(string $token): ?User
    {
        return $this->repository->findOneBy(['confirmationToken' => $token]);
    }
}
