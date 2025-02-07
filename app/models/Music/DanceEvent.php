<?php
require_once "MusicEvent.php";

class DanceEvent extends MusicEvent implements JsonSerializable
{
    private array $artists;

    public function __construct($id, $name, DateTime $startTime, DateTime $endTime, Location $location, EventType $eventType, array $artists, $availableTickets = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->setLocation($location);
        $this->setEventType($eventType);
        $this->setAvailableTickets($availableTickets);
        $this->setArtists($artists);
    }

    public function getArtists(): array
    {
        return $this->artists;
    }

    public function setArtists(array $value): void
    {
        $this->artists = $value;
    }

    public function addArtist(Artist $artist): void
    {
        $this->artists[] = $artist;
    }

    public function removeArtist(Artist $artist): void
    {
        $index = array_search($artist, $this->artists);
        if ($index !== false) {
            unset($this->artists[$index]);
        }
    }

    public function jsonSerialize(): mixed
    {
        return array_merge(parent::jsonSerialize(), [
            'artists' => $this->getArtists()
        ]);
    }
}
