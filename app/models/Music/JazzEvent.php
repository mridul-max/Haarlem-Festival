<?php
require_once "MusicEvent.php";

class JazzEvent extends MusicEvent implements JsonSerializable
{
    private Artist $artist;

    public function __construct($id, $name, DateTime $startTime, DateTime $endTime, Artist $artist, Location $location, EventType $eventType, $availableTickets = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->setArtist($artist);
        $this->setLocation($location);
        $this->setEventType($eventType);
        $this->setAvailableTickets($availableTickets);
    }

    public function getArtist(): Artist
    {
        return $this->artist;
    }

    public function setArtist(Artist $value)
    {
        $this->artist = $value;
    }

    public function jsonSerialize(): mixed
    {
        return array_merge(parent::jsonSerialize(), [
            'artist' => $this->getArtist()
        ]);
    }
}
