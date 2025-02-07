<?php
require(__dir__ . "/../models/Types/EventType.php");
class Event implements JsonSerializable
{
    private $id;
    private $name;
    private DateTime $startTime;
    private DateTime $endTime;
    private ?EventType $eventType;
    private $availableTickets;

    public function jsonSerialize(): mixed
    {
        $obj = [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "startTime" => $this->getStartTime(),
            "endTime" => $this->getEndTime(),
            "vat" => $this->getVat()
        ];

        if (isset($this->availableTickets) && $this->availableTickets != null && $this->availableTickets) {
            $obj["availableTickets"] = $this->availableTickets;
        }

        if (isset($this->eventType) && $this->eventType != null && $this->eventType) {
            $obj["eventType"] = $this->eventType;
        }

        return $obj;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $value)
    {
        $this->startTime = $value;
    }

    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(DateTime $value)
    {
        $this->endTime = $value;
    }

    public function getVat()
    {
        return $this->eventType->getVat();
    }

    public function getAvailableTickets()
    {
        if (!isset($this->availableTickets) || $this->availableTickets == null || !$this->availableTickets) {
            return null;
        }
        return $this->availableTickets;
    }

    public function setAvailableTickets($value)
    {
        $this->availableTickets = $value;
    }

    public function getEventType(): ?EventType
    {
        return $this->eventType;
    }

    public function setEventType(?EventType $value)
    {
        $this->eventType = $value;
    }
}
