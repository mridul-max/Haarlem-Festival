<?php

require_once('APIController.php');
require_once(__DIR__ . '/../../models/Event.php');
require_once(__DIR__ . '/../../models/Music/JazzEvent.php');
require_once(__DIR__ . '/../../services/EventService.php');
require_once(__DIR__ . '/../../services/EventTypeService.php');
require_once(__DIR__ . '/../../services/TicketTypeService.php');
require_once(__DIR__ . '/../../services/FestivalHistoryService.php');
require_once(__DIR__ . '/../../models/Types/TicketType.php');
require_once(__DIR__ . '/../../models/TicketLink.php');

require_once(__DIR__ . '/../../services/TicketLinkService.php');
require_once(__DIR__ . '/../../services/JazzTicketLinkService.php');
require_once(__DIR__ . '/../../services/HistoryTicketLinkService.php');
require_once(__DIR__ . '/../../services/PassTicketLinkService.php');
require_once(__DIR__ . '/../../services/LocationService.php');

class EventAPIController extends APIController
{
    private $eventService;
    private $ticketTypeService;
    private $eventTypeService;
    private $ticketLinkService;
    private $locationService;
    private $festivalHistoryservice;

    // Music services
    private $artistService;

    public const URI_JAZZ = "/api/events/jazz";
    public const URI_STROLL = "/api/events/stroll";
    public const URI_PASSES = "/api/events/passes";

    public function __construct()
    {
        $this->eventService = new EventService();
        $this->ticketTypeService = new TicketTypeService();
        $this->eventTypeService = new EventTypeService();
        $this->festivalHistoryservice = new FestivalHistoryService();
        $this->locationService = new LocationService();

        $request = $_SERVER['REQUEST_URI'];
        if (
            str_starts_with($request, EventAPIController::URI_JAZZ)
        ) {
            if (str_starts_with($request, EventAPIController::URI_JAZZ)) {
                $this->ticketLinkService = new JazzTicketLinkService();
            }
            require_once(__DIR__ . '/../../services/ArtistService.php');
            $this->artistService = new ArtistService();
        } elseif (str_starts_with($request, EventAPIController::URI_STROLL)) {
            $this->ticketLinkService = new HistoryTicketLinkService();
        } elseif (str_starts_with($request, EventAPIController::URI_PASSES)) {
            $this->ticketLinkService = new PassTicketLinkService();
        } else {
            $this->ticketLinkService = new TicketLinkService();
        }
    }

    public function handleGetRequest($uri)
    {
        $sort = $_GET['sort'] ?? 'time';
        $filters = isset($_GET) ? $_GET : [];

        // remove the 'sort' from filters
        unset($filters['sort']);

        // htmlspecialchars all the things
        $sort = htmlspecialchars($sort);
        $filters = array_map('htmlspecialchars', $filters);

        try {
            if (str_starts_with($uri, '/api/events/dates')) {
                $dates = $this->eventService->getFestivalDates($filters);
                echo json_encode($dates);
                return;
            } elseif (
                str_starts_with($uri, EventAPIController::URI_JAZZ)
            ) {
                if (isset($_GET['artist'])) {
                    $artistId = $_GET['artist'];
                    echo json_encode($this->eventService->getJazzEventsByArtistId($artistId));
                    return;
                }

                // Get the appropriate kind, or all artists if none is specified.
                if (str_starts_with($uri, EventAPIController::URI_JAZZ)) {
                    $filters['artist_kind'] = '1';
                }
            }

            if (is_numeric(basename($uri))) {
                echo json_encode($this->ticketLinkService->getByEventId(basename($uri)));
                return;
            }
            echo json_encode($this->ticketLinkService->getAll($sort, $filters));
        } catch (ObjectNotFoundException $e) {
            $this->sendErrorMessage("Event with given ID not found.", 404);
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrieve events.");
        }
    }

}