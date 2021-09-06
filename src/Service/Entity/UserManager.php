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
}
