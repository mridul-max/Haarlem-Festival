<?php

require_once("TicketLinkService.php");
require_once(__DIR__ . "/../repositories/HistoryTicketLinkRepository.php");
require_once("LocationService.php");

class HistoryTicketLinkService extends TicketLinkService
{
    private $locationService;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new HistoryTicketLinkRepository();
        $this->locationService = new LocationService();
    }

    public function getAll($sort = null, $filters = []): array
    {
        return $this->repo->getAll($sort, $filters);
    }

    public function getById(int $id): TicketLink
    {
        return $this->repo->getById($id);
    }

    public function getByEventId(int $id): TicketLink
    {
        $item = $this->repo->getByEventId($id);
        if ($item == null) {
            throw new Exception("TicketLink not found");
        }
        return $item;
    }
}
