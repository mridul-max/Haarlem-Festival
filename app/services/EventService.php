<?php

require_once(__DIR__ . "/../models/Event.php");
require_once(__DIR__ . "/../models/Music/JazzEvent.php");
require_once(__DIR__ . "/../repositories/EventRepository.php");
require_once(__DIR__ . "/../models/Exceptions/InvalidVariableException.php");
require_once(__DIR__ . "/../models/Exceptions/ObjectNotFoundException.php");
require_once('EventTypeService.php');

class EventService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new EventRepository();
    }

    public function getJazzEventsByArtistId($artistId): array
    {
        $artistId = htmlspecialchars($artistId);
        return $this->repo->getJazzEventsForArtist($artistId);
    }

    public function addEvent($event): Event
    {
        $event->setName(htmlspecialchars($event->getName()));
        // Do it only if availableTickets is NOT null.
        if ($event->getAvailableTickets() !== null) {
            $event->setAvailableTickets(htmlspecialchars($event->getAvailableTickets()));
        }

        if ($event->getStartTime() > $event->getEndTime()) {
            throw new InvalidVariableException("Start time cannot be after end time");
        }

        if ($event->getEventType() !== null) {
            $eventTypeId = htmlspecialchars($event->getEventType()->getId());
        }

        $id = $this->repo->createEvent(
            $event->getName(),
            $event->getStartTime(),
            $event->getEndTime(),
            $eventTypeId,
            $event->getAvailableTickets()
        );

        // if event is type of jazzevent
        if ($event instanceof JazzEvent) {
            $event->getArtist()->setId(htmlspecialchars($event->getArtist()->getId()));
            $event->getLocation()->setLocationId(htmlspecialchars($event->getLocation()->getLocationId()));
            $this->repo->createJazzEvent(
                $id,
                $event->getArtist()->getId(),
                $event->getLocation()->getLocationId()
            );

            return $this->repo->getJazzEventById($id);
        }

        // if event is type of history event
        if ($event instanceof HistoryEvent) {
            $event->getGuide()->setGuideId(htmlspecialchars($event->getGuide()->getGuideId()));
            $event->getLocation()->setLocationId(htmlspecialchars($event->getLocation()->getLocationId()));
            $this->repo->createHistoryEvent(
                $id,
                $event->getGuide()->getGuideId(),
                $event->getLocation()->getLocationId()
            );

            return $this->repo->getHistoryEventById($id); // Modify this line
        }

        return $this->repo->getEventById($id);
    }

    /**
     * @throws ObjectNotFoundException
     * @throws InvalidVariableException
     */
    public function editEvent($event): Event
    {
        //Check if event exists
        if (!$this->repo->getEventById($event->getId())) {
            throw new ObjectNotFoundException("Event does not exist", 404);
        }

        $event->setName(htmlspecialchars($event->getName()));
        $event->setId(htmlspecialchars($event->getId()));
        $event->setAvailableTickets(htmlspecialchars($event->getAvailableTickets()));

        if ($event->getStartTime() > $event->getEndTime()) {
            throw new InvalidVariableException("Start time cannot be after end time");
        }

        $eventTypeId = htmlspecialchars($event->getEventType()->getId());

        $this->repo->updateEvent(
            $event->getId(),
            $event->getName(),
            $event->getStartTime(),
            $event->getEndTime(),
            $eventTypeId,
            $event->getAvailableTickets()
        );

        // if event is type of jazzevent
        if ($event instanceof JazzEvent) {
            $event->getArtist()->setId(htmlspecialchars($event->getArtist()->getId()));
            $event->getLocation()->setLocationId(htmlspecialchars($event->getLocation()->getLocationId()));

            $this->repo->updateJazzEvent(
                $event->getId(),
                $event->getArtist()->getId(),
                $event->getLocation()->getLocationId()
            );
            return $this->repo->getEventById($event->getId());
        }

        if ($event instanceof HistoryEvent) {
            $event->getGuide()->setGuideId(htmlspecialchars($event->getGuide()->getGuideId()));
            $event->getLocation()->setLocationId(htmlspecialchars($event->getLocation()->getLocationId()));

            $this->repo->updateHistoryEvent(
                $event->getId(),
                $event->getGuide()->getGuideId(),
                $event->getLocation()->getLocationId()
            );
            return $this->repo->getEventById($event->getId());
        } else
            throw new InvalidVariableException("Event type not supported");
    }

    public function deleteEvent(int $id)
    {
        $this->repo->deleteById($id);
    }

    public function getFestivalDates($filters)
    {
        return $this->repo->getFestivalDates($filters);
    }
}
