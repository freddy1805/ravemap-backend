<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="ravemap__users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     * @Serializer\Groups({
     *     "user_list",
     *     "user_detail"
     * })
     */
    protected $id;

    /**
     * @var string
     * @Serializer\Groups({
     *     "user_list",
     *     "user_detail"
     * })
     */
    protected $username;

    /**
     * @var string
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $email;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     * @Serializer\Groups({
     *     "user_list",
     *     "user_detail"
     * })
     */
    protected $raverScore;

    /**
     * @var string
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $lastLogin;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $registeredAt;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="App\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invite", mappedBy="toUser")
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $invites;

    /**
     * @var Event[]
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $createdEvents = [];

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User", mappedBy="friends")
     */
    protected $friendsWithMe;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="ravemap__friends",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     */
    protected $friends;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->invites = new ArrayCollection();
        $this->raverScore = 0;
        $this->registeredAt = new DateTime();

        $this->friendsWithMe = new ArrayCollection();
        $this->friends = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRaverScore(): ?int
    {
        return $this->raverScore;
    }

    /**
     * @param int $raverScore
     * @return User
     */
    public function setRaverScore(int $raverScore): self
    {
        $this->raverScore = $raverScore;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    /**
     * @param Collection $invites
     * @return User
     */
    public function setInvites(Collection $invites): self
    {
        $this->invites = $invites;

        return $this;
    }

    /**
     * @param Invite $invite
     * @return User
     */
    public function addInvite(Invite $invite): self
    {
        if (!$this->invites->contains($invite)) {
            $this->invites->add($invite);
        }

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getCreatedEvents(): array
    {
        return $this->createdEvents;
    }

    /**
     * @param Event[] $createdEvents
     * @return User
     */
    public function setCreatedEvents(array $createdEvents): self
    {
        $this->createdEvents = $createdEvents;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegisteredAt(): DateTime
    {
        return $this->registeredAt;
    }

    /**
     * @param DateTime $registeredAt
     * @return User
     */
    public function setRegisteredAt(DateTime $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * @return Media
     */
    public function getImage(): ?Media
    {
        return $this->image;
    }

    /**
     * @param Media|null $image
     * @return User
     */
    public function setImage(?Media $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return User[]|Collection
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param UserInterface $user
     * @return User
     */
    public function addFriend(UserInterface $user): self
    {
        if (!$this->friends->contains($user)) {
            $this->friends->add($user);
        }

        return $this;
    }

    /**
     * @param UserInterface $user
     * @return User
     */
    public function removeFriend(UserInterface $user): self
    {
        $this->friends->removeElement($user);

        return $this;
    }

    /**
     * @return User[]|Collection
     */
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
    }

    /**
     * @param UserInterface $user
     * @return User
     */
    public function addFriendWithMe(UserInterface $user): self
    {
        if (!$this->friendsWithMe->contains($user)) {
            $this->friendsWithMe->add($user);
        }

        return $this;
    }

    /**
     * @param UserInterface $user
     * @return User
     */
    public function removeFriendWithMe(UserInterface $user): self
    {
        $this->friendsWithMe->removeElement($user);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->username;
    }
}
