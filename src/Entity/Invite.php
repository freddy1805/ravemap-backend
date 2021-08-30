<?php

namespace App\Entity;

use App\Repository\InviteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=InviteRepository::class)
 * @ORM\Table(name="ravemap__invites")
 */
class Invite
{
    const ROLE_MASTER = 'master';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_NORMAL_GUEST = 'normal';

    const STATUS_INVITE_ACCEPTED = 0;
    const STATUS_INVITE_MAYBE = 1;
    const STATUS_INVITE_DENIED = 2;
    const STATUS_INVITE_PENDING = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     * @Serializer\Groups({
     *     "invite_list",
     *     "invite_detail"
     * })
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
     * @Serializer\Groups({
     *     "invite_list",
     *     "invite_detail"
     * })
     */
    protected $fromUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invites")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
     * @Serializer\Groups({
     *     "invite_detail"
     * })
     */
    protected $toUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="invites")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * @Serializer\Groups({
     *     "invite_list",
     *     "invite_detail"
     * })
     * @Serializer\MaxDepth(depth=1)
     */
    protected $event;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Groups({
     *     "invite_list",
     *     "invite_detail"
     * })
     */
    protected $role;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Serializer\Groups({
     *     "invite_list",
     *     "invite_detail"
     * })
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Serializer\Groups({
     *     "invite_list",
     *     "invite_detail"
     * })
     */
    protected $invitedAt;

    /**
     * @var string
     * @Serializer\Groups({
     *     "invite_url"
     * })
     * @Serializer\SkipWhenEmpty()
     */
    protected $url;

    /**
     * Invite constructor.
     */
    public function __construct()
    {
        $this->toUser = null;
        $this->role = self::ROLE_NORMAL_GUEST;
        $this->status = self::STATUS_INVITE_PENDING;
        $this->invitedAt = new \DateTime();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return UserInterface
     */
    public function getFromUser(): ?UserInterface
    {
        return $this->fromUser;
    }

    /**
     * @param UserInterface $fromUser
     * @return Invite
     */
    public function setFromUser(UserInterface $fromUser): self
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getToUser(): ?UserInterface
    {
        return $this->toUser;
    }

    /**
     * @param UserInterface $toUser
     * @return Invite
     */
    public function setToUser(UserInterface $toUser): self
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @param $event
     * @return Invite
     */
    public function setEvent($event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return Invite
     */
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Invite
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getInvitedAt(): \DateTimeInterface
    {
        return $this->invitedAt;
    }

    /**
     * @param \DateTimeInterface $invitedAt
     * @return Invite
     */
    public function setInvitedAt(\DateTimeInterface $invitedAt): self
    {
        $this->invitedAt = $invitedAt;

        return $this;
    }

    /**
     * @param string $url
     * @return Invite
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->toUser) {
            return $this->getFromUser()->getUsername() . ' -> ' . $this->getToUser()->getUsername();
        }
        return $this->getFromUser()->getUsername() . ' -> ?';
    }
}
