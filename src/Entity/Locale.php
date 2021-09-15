<?php

namespace App\Entity;

use App\Repository\LocaleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocaleRepository::class)
 * @ORM\Table(name="ravemap__locale")
 */
class Locale
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $localeId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

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

    public function getLocaleId(): ?string
    {
        return $this->localeId;
    }

    public function setLocaleId(string $localeId): self
    {
        $this->localeId = $localeId;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->name) {
            return 'locale.new';
        }
        return $this->name . ' (' . $this->localeId . ')';
    }
}
