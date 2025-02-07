<?php

require_once("../models/Guide.php");
require_once("../models/Location.php");
require_once("../models/Event.php");

class HistoryEvent extends Event implements JsonSerializable
{
    private Guide $guide;
    private Location $location;

    public function __construct($id, $name, $availableTickets, DateTime $startTime, DateTime $endTime, Guide $guide, Location $location, EventType $eventType)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setAvailableTickets($availableTickets);
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->guide = $guide;
        $this->location = $location;
        $this->setEventType($eventType);
    }

    public function getGuide(): Guide
    {
        return $this->guide;
    }

    public function setGuide(Guide $value)
    {
        $this->guide = $value;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setLocation(Location $value)
    {
        $this->location = $value;
    }

    public function jsonSerialize(): mixed
    {
        return array_merge(parent::jsonSerialize(), [
            'guide' => $this->getGuide(),
            'location' => $this->getLocation()
        ]);
    }
}
