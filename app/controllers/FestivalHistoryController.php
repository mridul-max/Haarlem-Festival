<?php

require_once("../services/HistoryTicketLinkService.php");
require_once("../services/FestivalHistoryService.php");
require_once("../services/LocationService.php");

class FestivalHistoryController
{
    private $festivalHistoryService;
    private $locationService;

    private $ticketTypeService;

    private $eventService;
    private $ticketLinkService;

    public function __construct()
    {
        $this->ticketLinkService = new HistoryTicketLinkService();
        $this->festivalHistoryService = new FestivalHistoryService();
        $this->locationService = new LocationService();
        $this->ticketTypeService = new TicketTypeService();
        $this->eventService = new EventService();
    }
    public function loadHistoryStrollPage()
    {
        try {
            $cartItemService = new TicketLinkService();
            $historyTicketLinks = $cartItemService->getAll();

            require("../views/festival/history-stroll.php");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllHistoryEvents()
    {
        try {
            $historyEvents = $this->festivalHistoryService->getAllHistoryEvents();
            require("../views/admin/History Management/manageHistory.php");

            return $historyEvents;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function addTour()
    {
        try {
            $guides = $this->festivalHistoryService->getAllGuides();
            $locations = $this->locationService->getAll();
            $ticketTypes = $this->ticketTypeService->getAll();
            require("../views/admin/History Management/addTour.php");


            return $guides
                && $locations && $ticketTypes;
        } catch (PDOException $e) {
        }
    }
}
