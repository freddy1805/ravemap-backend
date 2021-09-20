<?php

namespace App\Message;

use App\Entity\Event;

class EventRemovedMessage {

    /**
     * @var string
     */
    private string $eventId;

    /**
     * @var Event
     */
    private Event $event;

    /**
     * @var string|null
     */
    private ?string $reason = null;

    /**
     * EventRemovedMessage constructor.
     * @param Event|null $event
     * @param string|null $eventId
     * @param string|null $reason
     */
    public function __construct(?Event $event = null, string $eventId = null, string $reason = null)
    {
        if ($event) {
            $this->event = $event;
        }

        if ($eventId) {
            $this->eventId = $eventId;
        }

        if ($reason) {
            $this->reason = $reason;
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

    /**
     * @return string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}
