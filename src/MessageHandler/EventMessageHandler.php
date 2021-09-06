<?php

namespace App\MessageHandler;

use App\Message\EventMessage;
use RedjanYm\FCMBundle\FCMClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EventMessageHandler implements MessageHandlerInterface
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
     * @param EventMessage $eventMessage
     */
    public function __invoke(EventMessage $eventMessage)
    {
        $message = $eventMessage->getPost();

        $notification = $this->FCMClient->createTopicNotification(
            $message->getAuthor()->getUsername() . ' @ ' . $message->getEvent()->getName(),
            $message->getContent(),
            'event-' . $message->getEvent()->getId(),
        );

        $this->FCMClient->sendNotification($notification);
    }
}
