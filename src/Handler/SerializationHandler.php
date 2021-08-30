<?php

namespace App\Handler;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        ];
    }
}
