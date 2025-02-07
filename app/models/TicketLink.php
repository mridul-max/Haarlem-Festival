<?php

require_once('Event.php');
require_once(__DIR__ . '/Types/TicketType.php');

/**
 * A link between an event and a ticket type.
 */
class TicketLink implements JsonSerializable
{
    private $id;
    private Event $event;
    private TicketType $ticketType;

    public function __construct($id, Event $event, TicketType $ticketType)
    {
        $this->id = $id;
        $this->event = $event;
        $this->ticketType = $ticketType;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'event' => $this->getEvent(),
            'ticketType' => $this->getTicketType()
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $value)
    {
        $this->id = $value;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $value)
    {
        $this->event = $value;
    }

    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }

    public function setTicketType(TicketType $value)
    {
        $this->ticketType = $value;
    }
}
