<?php

namespace App\Handler;

use App\Entity\Media;
use App\Entity\SonataMediaMedia;
use App\Entity\User;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SerializationHandler
 * @package App\Handler
 */
class SerializationHandler implements SubscribingHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SerializationHandler constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Media::class,
                'method' => 'serializeMediaType',
            ]
        ];
    }

    /**
     * Serialize the media entity for the api
     * @param JsonSerializationVisitor $visitor
     * @param Media $media
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeMediaType(
        JsonSerializationVisitor $visitor,
        Media $media,
        array $type,
        Context $context
    ) {
        /** @var MediaProviderInterface $provider */
        $provider = $this->container->get($media->getProviderName());
        $mediaContext = $media->getContext();

        /** @var RouterInterface $urlGenerator */
        $urlGenerator = $this->container->get('router');
        $routeContext = $urlGenerator->getContext();
        $urlPrefixPath = 'https://'.$routeContext->getHost();

        return [
            'id' => $media->getId(),
            'type' => $media->getContentType(),
            'large' => $urlPrefixPath.$provider->generatePublicUrl($media,  $mediaContext.'_large'),
            'medium' => $urlPrefixPath.$provider->generatePublicUrl($media,  $mediaContext.'_medium'),
            'small' => $urlPrefixPath.$provider->generatePublicUrl($media,  $mediaContext.'_small')
        ];
    }
}
