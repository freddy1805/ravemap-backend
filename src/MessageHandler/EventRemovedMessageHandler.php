<?php

namespace App\MessageHandler;

use App\Message\EventRemovedMessage;
use RedjanYm\FCMBundle\FCMClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EventRemovedMessageHandler implements MessageHandlerInterface
{
    const DEFAULT_REASON = 'Die Veranstaltung wurde leider abgesagt!';
    const CUSTOM_REASON = 'Die Veranstaltung wurde leider wegen %s abgesagt!';

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
        $message = self::DEFAULT_REASON;

        if ($reason = $eventRemovedMessage->getReason()) {
            $message = sprintf(self::CUSTOM_REASON, $reason);
        }

        $notification = $this->FCMClient->createTopicNotification(
            $event->getName(),
            $message,
            'event-' . $eventRemovedMessage->getEventId(),
        );

        $this->FCMClient->sendNotification($notification);
    }
}
