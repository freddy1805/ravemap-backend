<?php

namespace App\Websocket\Topic;

use App\Entity\Event;
use App\Message\EventMessage;
use App\Service\Entity\EventManager;
use Gos\Bundle\WebSocketBundle\Authentication\ConnectionRepositoryInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Psr\Cache\InvalidArgumentException;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventTopic implements TopicInterface
{
    /**
     * @var ConnectionRepositoryInterface
     */
    private ConnectionRepositoryInterface $connectionRepository;

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    private MessageBusInterface $messageBus;

    /**
     * EventTopic constructor.
     * @param ConnectionRepositoryInterface $connectionRepository
     * @param EventManager $eventManager
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        ConnectionRepositoryInterface $connectionRepository,
        EventManager $eventManager,
        MessageBusInterface $messageBus
    ) {
        $this->connectionRepository = $connectionRepository;
        $this->eventManager = $eventManager;
        $this->messageBus = $messageBus;
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $eventId = $request->getAttributes()->get('event_ref');

        /** @var Event $event */
        $event = $this->eventManager->getById($eventId);

        // This will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['messages' => $this->eventManager->makeArrayMessages($event->getPosts()->toArray())]);
    }

    /**
     * This will receive any unsubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     *
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        // This will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId . ' has left ' . $topic->getId()]);
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param mixed $event
     * @param array $exclude
     * @param array $eligible
     * @return void
     */
    public function onPublish(
        ConnectionInterface $connection,
        Topic $topic,
        WampRequest $request,
        $event,
        array $exclude,
        array $eligible
    )
    {
        $user = $this->connectionRepository->getUser($connection);
        $eventId = $request->getAttributes()->get('event_ref');

        /** @var Event $raveMapEvent */
        $raveMapEvent = $this->eventManager->getById($eventId);
        $messages = $this->eventManager->postNewMessage($user, $raveMapEvent, $event['msg']);

        $newest = $messages['new'];
        $this->messageBus->dispatch(new EventMessage($newest));

        $topic->broadcast($messages);
    }

    /**
     * Like RPC the name is used to identify the channel
     *
     * @return string
     */
    public function getName(): string
    {
        return 'event.messages.topic';
    }
}
