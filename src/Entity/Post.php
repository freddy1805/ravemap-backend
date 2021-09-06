<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\Table(name="ravemap__posts")
 */
class Post
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="posts")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    protected $event;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    protected $author;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $content;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $timestamp;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->timestamp = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     * @return Post
     */
    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param UserInterface $author
     * @return Post
     */
    public function setAuthor(UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Post
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'author' => [
                'id' => $this->author->getId(),
                'username' => $this->author->getUsername(),
                'image' => [
                    'medium' => ''
                ]
            ],
            'content' => $this->content,
            'timestamp' => $this->timestamp->getTimestamp(),
        ];
    }
}
