<?php
require_once(__DIR__ . '/../Event.php');
require_once('Artist.php');
require_once(__DIR__ . '/../Types/EventType.php');
require_once(__DIR__ . '/../Location.php');

class JazzBase extends Event implements JsonSerializable
{
    protected Location $location;

    public function __construct($id, $name, DateTime $startTime, DateTime $endTime, Location $location, EventType $eventType, $availableTickets = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->setLocation($location);
        $this->setEventType($eventType);
        $this->setAvailableTickets($availableTickets);
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
            'location' => $this->getLocation()
        ]);
    }
}