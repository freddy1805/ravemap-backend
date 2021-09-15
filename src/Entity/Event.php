<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="ravemap__events")
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    const LOCATION_NAME_KEY = 'name';
    const LOCATION_CITY_KEY = 'city';
    const LOCATION_COUNTRY_KEY = 'country';

    const MODE_INVITE = 0;
    const MODE_MOD_INVITE = 1;
    const MODE_PRIVATE = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     * @Serializer\Groups({
     *     "event_list",
     *     "event_detail"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Groups({
     *     "event_list",
     *     "event_detail"
     * })
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Groups({
     *     "event_detail"
     * })
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @Serializer\Groups({
     *     "event_list",
     *     "event_detail"
     * })
     */
    private $date;

    /**
     * @ORM\Column(type="array")
     * @Serializer\Groups({
     *     "event_location"
     * })
     */
    private $location = [];

    /**
     * @var string
     * @Serializer\Groups({
     *     "event_list",
     *     "event_detail"
     * })
     * @Serializer\Accessor(getter="getDisplayedLocation")
     */
    private $displayedLocation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * @Serializer\Groups({
     *     "event_detail"
     * })
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invite", mappedBy="event")
     * @ORM\JoinTable(name="ravemap__event_invites")
     * @ORM\OrderBy({"status" = "ASC"})
     * @Serializer\Groups({
     *     "event_invites"
     * })
     */
    private $invites;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({
     *     "event_detail"
     * })
     */
    private $approval;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\Groups({
     *     "event_detail"
     * })
     */
    private $maxInvites;

    /**
     * @var int
     * @Serializer\Groups({
     *     "event_detail"
     * })
     * @Serializer\Accessor(getter="getAvailableInvites")
     */
    private $availableInvites;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="event")
     * @ORM\OrderBy({"timestamp" = "ASC"})
     * @Serializer\Groups({
     *     "event_messages"
     * })
     */
    private $posts;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $eventMode;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $route;

    /**
     * Event constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null, string $id = null)
    {
        if ($name) {
            $this->name = $name;
        }
        if ($id) {
            $this->id = $id;
        }
        $this->invites = new ArrayCollection();
        $this->date = new \DateTime();
        $this->posts = new ArrayCollection();
        $this->maxInvites = 50;
        $this->eventMode = self::MODE_INVITE;
        $this->route = [];
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Event
     */
    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLocation(): ?array
    {
        return $this->location;
    }

    public function setLocation(array $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayedLocation(): string
    {
        return $this->location[self::LOCATION_NAME_KEY] ?? '';
    }

    public function getCreator(): ?UserInterface
    {
        return $this->creator;
    }

    public function setCreator(UserInterface $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getInvites(): Collection
    {
        return $this->invites;
    }

    public function setInvites(Collection $invites): self
    {
        $this->invites = $invites;

        return $this;
    }

    public function addInvite(Invite $invite): self
    {
        if (!$this->invites->contains($invite)) {
            $this->invites->add($invite);
        }

        return $this;
    }

    public function removeInvite(Invite $invite): self
    {
        $this->invites->removeElement($invite);

        return $this;
    }

    public function getApproval(): ?bool
    {
        return $this->approval;
    }

    public function setApproval(bool $approval): self
    {
        $this->approval = $approval;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxInvites(): ?int
    {
        return $this->maxInvites;
    }

    /**
     * @param int $maxInvites
     * @return Event
     */
    public function setMaxInvites(int $maxInvites): self
    {
        $this->maxInvites = $maxInvites;

        return $this;
    }

    /**
     * @return int
     */
    public function getAvailableInvites(): int
    {
        return $this->maxInvites - $this->invites->count();
    }

    /**
     * @return Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param Collection $posts
     * @return Event
     */
    public function setPosts(Collection $posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * @param Post $post
     * @return Event
     */
    public function addPost(Post $post): self
    {
        $this->posts->add($post);

        return $this;
    }

    /**
     * @return int
     */
    public function getEventMode(): ?int
    {
        return $this->eventMode;
    }

    /**
     * @param int $eventMode
     * @return Event
     */
    public function setEventMode(int $eventMode): self
    {
        $this->eventMode = $eventMode;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoute(): array
    {
        return $this->route;
    }

    /**
     * @param array $route
     * @return Event
     */
    public function setRoute(array $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->name;
    }
}
