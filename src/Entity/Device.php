<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class Device
 * @package App\Entity
 * @ORM\Table(name="ravemap__devices")
 * @ORM\Entity(repositoryClass=DeviceRepository::class)
 */
class Device {

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="devices")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     * @Serializer\Groups({
     *     "user_device"
     * })
     */
    private $deviceId;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     * @Serializer\Groups({
     *     "user_device"
     * })
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Serializer\Groups({
     *     "user_device"
     * })
     */
    private $os;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Serializer\Groups({
     *     "user_device"
     * })
     */
    private $appVersion;

    /**
     * @var string
     * @ORM\Column(type="text", unique=true, nullable=false)
     */
    private $firebaseToken;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({
     *     "user_device"
     * })
     */
    private $registeredAt;

    /**
     * Device constructor.
     */
    public function __construct()
    {
        $this->registeredAt = new \DateTime('now');
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Device
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    /**
     * @param string $deviceId
     * @return Device
     */
    public function setDeviceId(string $deviceId): self
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Device
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getOs(): ?string
    {
        return $this->os;
    }

    /**
     * @param string $os
     * @return Device
     */
    public function setOs(string $os): self
    {
        $this->os = $os;

        return $this;
    }

    /**
     * @return string
     */
    public function getAppVersion(): ?string
    {
        return $this->appVersion;
    }

    /**
     * @param string $appVersion
     * @return Device
     */
    public function setAppVersion(string $appVersion): self
    {
        $this->appVersion = $appVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirebaseToken(): ?string
    {
        return $this->firebaseToken;
    }

    /**
     * @param string $firebaseToken
     * @return Device
     */
    public function setFirebaseToken(string $firebaseToken): self
    {
        $this->firebaseToken = $firebaseToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegisteredAt(): \DateTime
    {
        return $this->registeredAt;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name . ' (' . $this->os . ') - ' . $this->appVersion;
    }
}
