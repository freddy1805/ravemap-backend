<?php

namespace App\Service\Entity;

use App\Entity\Invite;

class InviteManager extends BaseManager {

    protected string $repoName = 'App:Invite';

    protected array $validation = [
        'fromUser',
        'role',
        'event'
    ];

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Invite::class;
    }
}
