<?php

require_once(__DIR__ . '/../../models/Event.php');
require_once(__DIR__ . '/../../models/Music/MusicEvent.php');
require_once(__DIR__ . '/../../models/Music/JazzEvent.php');
require_once(__DIR__ . '/../../models/Music/DanceEvent.php');
require_once(__DIR__ . '/../../services/EventService.php');
require_once(__DIR__ . '/../../services/EventTypeService.php');
require_once(__DIR__ . '/../../services/TicketTypeService.php');
require_once(__DIR__ . '/../../services/FestivalHistoryService.php');
require_once('APIController.php');
require_once(__DIR__ . '/../../models/Types/TicketType.php');
require_once(__DIR__ . '/../../models/TicketLink.php');

require_once(__DIR__ . '/../../services/TicketLinkService.php');
require_once(__DIR__ . '/../../services/JazzTicketLinkService.php');
require_once(__DIR__ . '/../../services/DanceTicketLinkService.php');
require_once(__DIR__ . '/../../services/HistoryTicketLinkService.php');
require_once(__DIR__ . '/../../services/PassTicketLinkService.php');
require_once(__DIR__ . '/../../services/LocationService.php');

/**
 * @author Konrad
 */
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
    public const URI_DANCE = "/api/events/dance";
    public const URI_STROLL = "/api/events/stroll";
    public const URI_YUMMY = "/api/events/yummy";
    public const URI_PASSES = "/api/events/passes";

    public function __construct()
    {
        $this->eventService = new EventService();
        $this->ticketTypeService = new TicketTypeService();
        $this->eventTypeService = new EventTypeService();
        $this->festivalHistoryservice = new FestivalHistoryService();
        $this->locationService = new LocationService();

        // Load appropriate TicketLinkService.
        $request = $_SERVER['REQUEST_URI'];
        if (
            str_starts_with($request, EventAPIController::URI_JAZZ)
            || str_starts_with($request, EventAPIController::URI_DANCE)
        ) {
            if (str_starts_with($request, EventAPIController::URI_JAZZ)) {
                $this->ticketLinkService = new JazzTicketLinkService();
            } else {
                $this->ticketLinkService = new DanceTicketLinkService();
            }

            // Music Services
            require_once(__DIR__ . '/../../services/ArtistService.php');
            $this->artistService = new ArtistService();
        } elseif (str_starts_with($request, EventAPIController::URI_STROLL)) {
            $this->ticketLinkService = new HistoryTicketLinkService();
        } elseif (str_starts_with($request, EventAPIController::URI_PASSES)) {
            $this->ticketLinkService = new PassTicketLinkService();
        } else {
            // Load the generic TicketLinkService.
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
                || str_starts_with($uri, EventAPIController::URI_DANCE)
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

    public function handlePostRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        try {
            // Valid ticket type is required.
            // Check /api/tickettypes for available ticket types.
            $ticketTypeId = $data['ticketType']['id'];
            if (!isset($ticketTypeId)) {
                $ticketTypeId = $data['ticketType'];
            }
            if (!isset($ticketTypeId)) {
                $ticketTypeId = $data['ticketTypeId'];
            }

            $ticketType = $this->ticketTypeService->getById($ticketTypeId);
            $event = null;

            if (
                str_starts_with($uri, EventAPIController::URI_JAZZ)
            ) {
                $artist = $this->artistService->getById($data['event']['artistId']);
                $location = $this->locationService->getById($data['event']['locationId']);

                // In terms of music events, the capacity is the number of available seats.
                $availableSeats = $location->getCapacity();

                $eventType = $this->eventTypeService->getById($data['event']['eventTypeId']);

                $event = new JazzEvent(
                    0,
                    $data['event']['name'],
                    new DateTime($data['event']['startTime']),
                    new DateTime($data['event']['endTime']),
                    $artist,
                    $location,
                    $eventType,
                    $availableSeats,
                );
            } elseif (str_starts_with($uri, EventAPIController::URI_DANCE)) {
                $event = $this->buildDanceEventFromData($data);
            } elseif (str_starts_with($uri, EventAPIController::URI_STROLL)) {
                $guide = $this->festivalHistoryservice->getGuideById($data['guide']);
                $location = $this->locationService->getById($data['location']);
                $ticketTypeId = $data['ticketType'];

                $eventType = $this->eventTypeService->getById(3);

                $event = new HistoryEvent(
                    0,
                    $data['name'],
                    $data['available_tickets'],
                    new DateTime($data['start_time']),
                    new DateTime($data['end_time']),
                    $guide,
                    $location,
                    $eventType,
                );
            } else {
                // if availableTickets is not set, it is a pass.
                if (isset($data['event']['availableTickets'])) {
                    $this->sendErrorMessage('Invalid request', 400);
                    return;
                }

                $event = new Event();
                $event->setId($data['event']['id']);
                $event->setName($data['event']['name']);
                $event->setStartTime(new DateTime($data['event']['startTime']));
                $event->setEndTime(new DateTime($data['event']['endTime']));

                $eventType = $this->eventTypeService->getById($data['event']['eventType']['id']);
                $event->setEventType($eventType);
            }

            $ticketLink = new TicketLink(0, $event, $ticketType);

            $ticketLink = $this->ticketLinkService->add($ticketLink);

            echo json_encode($ticketLink);
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to post event(s).", 500);
        }
    }

    public function handlePutRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $editedTicketLinkID = (int)basename($uri);
            $ticketTypeId = $data['ticketType']['id'];
            if (!isset($ticketTypeId)) {
                $ticketTypeId = $data['ticketType'];
            }
            if (!isset($ticketTypeId)) {
                $ticketTypeId = $data['ticketTypeId'];
            }

            $ticketType = $this->ticketTypeService->getById($ticketTypeId);

            $event = null;

            if (str_starts_with($uri, EventAPIController::URI_JAZZ)) {
                $artist = $this->artistService->getById($data['event']['artistId']);
                $location = $this->locationService->getById($data['event']['locationId']);

                $availableSeats = $location->getCapacity();

                $eventType = $this->eventTypeService->getById($data['event']['eventTypeId']);

                $event = new JazzEvent(
                    basename($uri),
                    $data['event']['name'],
                    new DateTime($data['event']['startTime']),
                    new DateTime($data['event']['endTime']),
                    $artist,
                    $location,
                    $eventType,
                    $availableSeats
                );
            } elseif (str_starts_with($uri, EventAPIController::URI_DANCE)) {
                $event = $this->buildDanceEventFromData($data);
                $event->setId(basename($uri));
            } elseif (str_starts_with($uri, EventAPIController::URI_STROLL)) {
                $guide = $this->festivalHistoryservice->getGuideById($data['guide']);
                $location = $this->locationService->getById($data['location']);
                $ticketTypeId = $data['ticketType'];

                $eventType = $this->eventTypeService->getById(3);

                $event = new HistoryEvent(
                    $data['eventId'],
                    $data['name'],
                    $data['available_tickets'],
                    new DateTime($data['start_time']),
                    new DateTime($data['end_time']),
                    $guide,
                    $location,
                    $eventType,
                );
            } else {
                // if availableTickets is not set, it is an all access pass.
                if (isset($data['event']['availableTickets'])) {
                    $this->sendErrorMessage('Invalid request', 400);
                    return;
                }

                $event = new Event();
                $event->setId(basename($uri));
                $event->setName($data['event']['name']);
                $event->setStartTime(new DateTime($data['event']['startTime']));
                $event->setEndTime(new DateTime($data['event']['endTime']));

                $eventType = null;
                if (isset($data['event']['eventType'])) {
                    $eventType = new EventType(
                        $data['event']['eventType']['id'],
                        $data['event']['eventType']['name'],
                        $data['event']['eventType']['vat']
                    );
                }

                $event->setEventType($eventType);
            }

            $ticketLink = new TicketLink($editedTicketLinkID, $event, $ticketType);
            $ticketLink = $this->ticketLinkService->update($ticketLink);

            echo json_encode($ticketLink);
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to edit event.", 500);
        }
    }

    public function handleDeleteRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        try {
            $deleteEventId = basename($uri);
            $ci = $this->ticketLinkService->getByEventId($deleteEventId);
            $this->ticketLinkService->delete($ci);

            $ciId = $ci->getId();
            $eventId = $ci->getEvent()->getId();

            $this->sendSuccessMessage("Cart Item $ciId and event $eventId deleted.");
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to delete the event.", 500);
        }
    }

    /**
     * @throws Exception
     */
    private function buildDanceEventFromData($data): DanceEvent
    {
        //Fetch the location and number of available seats
        $location = $this->locationService->getById($data['event']['locationId']);
        $availableSeats = $location->getCapacity();

        //Fetch the event type
        $eventType = $this->eventTypeService->getById($data['event']['eventTypeId']);

        //Fetch the artists
        $artists = array();

        foreach ($data['event']['artistIds'] as $artistId) {
            $artists[] = $this->artistService->getById($artistId);
        }

        //Create and return the dance event
        return new DanceEvent(
            0,
            $data['event']['name'],
            new DateTime($data['event']['startTime']),
            new DateTime($data['event']['endTime']),
            $location,
            $eventType,
            $artists,
            $availableSeats
        );
    }
}
