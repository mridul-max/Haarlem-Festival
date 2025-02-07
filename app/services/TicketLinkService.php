<?php

require_once(__DIR__ . "/../repositories/TicketLinkRepository.php");
require_once("EventService.php");
require_once("TicketTypeService.php");
require_once(__DIR__ . "/../models/Exceptions/ObjectNotFoundException.php");

class TicketLinkService
{
    protected TicketLinkRepository $repo;
    private EventService $eventService;
    private TicketTypeService $ticketTypeService;

    public function __construct()
    {
        $this->repo = new TicketLinkRepository();
        $this->eventService = new EventService();
        $this->ticketTypeService = new TicketTypeService();
    }

    public function getAll($sort = null, $filters = []): array
    {
        return $this->repo->getAll($sort, $filters);
    }

    public function getById(int $id): ?TicketLink
    {
        return $this->repo->getById($id);
    }

    public function getByEventId(int $id): ?TicketLink
    {
        $item = $this->repo->getByEventId($id);
        if ($item == null) {
            throw new ObjectNotFoundException("Ticket Link not found");
        }
        return $item;
    }

    public function add(TicketLink $ticketLink): ?TicketLink
    {
        $ticketType = $this->ticketTypeService->getById($ticketLink->getTicketType()->getId());
        $event = $this->eventService->addEvent($ticketLink->getEvent());

        $id = $this->repo->insert($event->getId(), $ticketType->getId());
        return $this->getById($id);
    }

    public function update(TicketLink $ticketLink): ?TicketLink
    {
        $id = htmlspecialchars($ticketLink->getId());
        $eventId = htmlspecialchars($ticketLink->getEvent()->getId());
        $ticketTypeId = htmlspecialchars($ticketLink->getTicketType()->getId());

        $eventService = new EventService();
        $eventService->editEvent($ticketLink->getEvent());

        $this->repo->update($id, $eventId, $ticketTypeId);

        return $this->getByEventId($eventId);
    }

    public function delete(TicketLink $ticketLink): void
    {
        $eventService = new EventService();
        $eventService->deleteEvent($ticketLink->getEvent()->getId());

        $id = htmlspecialchars($ticketLink->getId());
        $this->repo->delete($id);
    }
}
