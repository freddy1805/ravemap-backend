<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseMedia;

/**
 * @ORM\Entity
 * @ORM\Table(name="ravemap__media")
 */
class Media extends BaseMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\MediaGalleryHasMedia",
     *     mappedBy="media", cascade={"persist"}, orphanRemoval=false
     * )
     *
     * @var MediaGalleryHasMedia[]
     */
    protected $galleryHasMedias;


    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        parent::prePersist();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        parent::preUpdate();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
