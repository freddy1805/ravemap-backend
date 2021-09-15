<?php

namespace App\MessageHandler;

use App\Message\EventRemovedMessage;
use RedjanYm\FCMBundle\FCMClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EventRemovedMessageHandler implements MessageHandlerInterface
{
    /**
     * @var FCMClient
     */
    private FCMClient $FCMClient;

    /**
     * EventMessageHandler constructor.
     * @param FCMClient $FCMClient
     */
    public function __construct(FCMClient $FCMClient)
    {
        $this->FCMClient = $FCMClient;
    }

    /**
     * @param EventRemovedMessage $eventRemovedMessage
     */
    public function __invoke(EventRemovedMessage $eventRemovedMessage)
    {
        $event = $eventRemovedMessage->getEvent();

        $notification = $this->FCMClient->createTopicNotification(
            $event->getName(),
            'Die Veranstaltung wurde abgesagt!',
            'event-' . $eventRemovedMessage->getEventId(),
        );

        $this->FCMClient->sendNotification($notification);
    }
}
