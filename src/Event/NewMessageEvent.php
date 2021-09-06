<?php

namespace App\Event;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

class NewMessageEvent extends BaseEvent {

    public const NAME = 'event.new_message';

    protected Post $post;

    /**
     * NewMessageEvent constructor.
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }}
