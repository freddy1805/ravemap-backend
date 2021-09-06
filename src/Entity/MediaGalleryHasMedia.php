<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGalleryHasMedia;

/**
 * @ORM\Entity
 * @ORM\Table(name="ravemap__media_gallery_has_media")
 */
class MediaGalleryHasMedia extends BaseGalleryHasMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Media",
     *     inversedBy="galleryHasMedias", cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Media
     */
    protected $media;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\MediaGallery",
     *     inversedBy="galleryHasMedias", cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var MediaGallery
     */
    protected $gallery;
}
