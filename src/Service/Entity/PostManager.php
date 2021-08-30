<?php

namespace App\Service\Entity;

use App\Entity\Post;

class PostManager extends BaseManager {

    protected string $repoName = 'App:Post';

    protected array $validation = [
        'event',
        'author',
        'content'
    ];

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Post::class;
    }
}
