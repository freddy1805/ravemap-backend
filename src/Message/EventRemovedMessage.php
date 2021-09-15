<?php

namespace App\Message;

use App\Entity\Event;

class EventRemovedMessage {

    private string $eventId;

    private Event $event;

    /**
     * EventRemovedMessage constructor.
     * @param Event|null $event
     * @param string|null $eventId
     */
    public function __construct(?Event $event = null, string $eventId = null)
    {
        if ($event) {
            $this->event = $event;
        }

        if ($eventId) {
            $this->eventId = $eventId;
        }
    }

    /**
     * @return Event|null
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getEventId(): string
    {
        return $this->eventId;
    }
}
