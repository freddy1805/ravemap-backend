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
     * @param $username
     * @return mixed
     */
    public function generateUniqueUsername($username)
    {
        $numSufix = explode('-', date('Y-m-d-H'));

        $userNamesList = [
            $username,
            $username.$numSufix[0],
            $username.$numSufix[1],
            $username.$numSufix[2],
            $username.$numSufix[3]
        ];

        foreach ($userNamesList as $item) {
            if ($this->isUsernameAvailable($item)) {
                return $item;
            }
        }

        return '';
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
