<?php

namespace App\Entity;

use App\Repository\StaticPageRepository;
use App\Util\EntityMapper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StaticPageRepository::class)
 * @ORM\Table(name="ravemap__static_pages")
 */
class StaticPage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var Locale
     * @ORM\ManyToOne(targetEntity="App\Entity\Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $locale;

    /**
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var int
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $navPosition;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inFooterNav = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inMainNav = false;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Locale
     */
    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    /**
     * @param Locale $locale
     * @return StaticPage
     */
    public function setLocale(Locale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        $this->slug = EntityMapper::slugify($title);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return int
     */
    public function getNavPosition(): ?int
    {
        return $this->navPosition;
    }

    /**
     * @param int $navPosition
     * @return StaticPage
     */
    public function setNavPosition(int $navPosition): self
    {
        $this->navPosition = $navPosition;

        return $this;
    }

    public function getInFooterNav(): ?bool
    {
        return $this->inFooterNav;
    }

    public function setInFooterNav(bool $inFooterNav): self
    {
        $this->inFooterNav = $inFooterNav;

        return $this;
    }

    public function getInMainNav(): ?bool
    {
        return $this->inMainNav;
    }

    public function setInMainNav(bool $inMainNav): self
    {
        $this->inMainNav = $inMainNav;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title ?? 'Neues Dokument';
    }
}
