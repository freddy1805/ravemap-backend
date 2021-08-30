<?php

namespace App\Controller\Api;

use App\Handler\SerializationHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseApiController extends AbstractController
{
    const JSON_CONTENT_TYPE = 'application/json';

    /**
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * BaseApiController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->serializer = SerializerBuilder::create()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new SerializationHandler($this->container));
            })
            ->addDefaultHandlers()
            ->build();
    }

    /**
     * @param mixed $data
     * @param array $groups
     * @return string
     */
    public function serializeToJson($data, array $groups = [])
    {
        $serialContext = new SerializationContext();
        $serialContext
            ->enableMaxDepthChecks()
            ->setGroups($groups);

        return $this->serializer->serialize($data, 'json', $serialContext);
    }
}
