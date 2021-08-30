<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as Serializer;

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
     * @ORM\OneToMany (targetEntity="App\Entity\Invite", mappedBy="toUser")
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $invites;

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
     * @var Event[]
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $createdEvents = [];

    /**
     * @var string
     * @Serializer\Groups({
     *     "user_detail"
     * })
     */
    protected $lastLogin;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->invites = new ArrayCollection();
        $this->raverScore = 0;
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
}
