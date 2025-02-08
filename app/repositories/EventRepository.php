<?php

require_once(__DIR__ . "/../models/Event.php");
require_once(__DIR__ . "/../models/History/HistoryEvent.php");
require_once(__DIR__ . "/../models/Music/JazzEvent.php");
require_once("Repository.php");
require_once("LocationRepository.php");
require_once("ArtistRepository.php");
require_once("EventTypeRepository.php");

class EventRepository extends Repository
{
    private $locationRepo;
    private $artistRepo;
    private $eventTypeRepo;

    public function __construct()
    {
        parent::__construct();

        $this->locationRepo = new LocationRepository();
        $this->artistRepo = new ArtistRepository();
        $this->eventTypeRepo = new EventTypeRepository();
    }

    private function buildEvent($arr): array
    {
        $events = [];
        foreach ($arr as $event) {
            if ($this->isInJazzEvents($event['eventId'])) {
                $events[] = $this->getJazzEventById($event['eventId']);
            } elseif ($this->isInHistoryEvents($event['eventId'])) {
                $events[] = $this->getHistoryEventById($event['eventId']);
            } else {
                $eventEntry = new Event();
                $eventEntry->setId($event['eventId']);
                $eventEntry->setName($event['name']);
                $eventEntry->setStartTime(new DateTime($event['startTime']));
                $eventEntry->setEndTime(new DateTime($event['endTime']));
                // if festivalEventType is not null
                if ($event['festivalEventType'] !== null) {
                    $eventEntry->setEventType($this->eventTypeRepo->getById($event['festivalEventType']));
                }
                if ($event['availableTickets'] !== null) {
                    $eventEntry->setAvailableTickets($event['availableTickets']);
                }
                array_push($events, $eventEntry);
            }
        }
        return $events;
    }

    private function buildJazzEvent($arr): array
    {
        $events = [];

        foreach ($arr as $event) {
            $event = new JazzEvent(
                $event['eventId'],
                $event['name'],
                new DateTime($event['startTime']),
                new DateTime($event['endTime']),
                $this->artistRepo->getById($event['artistId']),
                $this->locationRepo->getById($event['locationId']),
                $this->eventTypeRepo->getById($event['festivalEventType']),
                $event['availableTickets']
            );

            array_push($events, $event);
        }
        return $events;
    }
    public function getAll()
    {
        $sql = "SELECT eventId, name, startTime, endTime, festivalEventType FROM events";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $arr = $stmt->fetchAll();
        return $this->buildEvent($arr);
    }

    public function getEventById($id): ?Event
    {
        $sql = "SELECT eventId, name, startTime, endTime, festivalEventType, availableTickets FROM events WHERE eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $arr = $stmt->fetchAll();
        $output = $this->buildEvent($arr);

        if (empty($output)) {
            return null;
        }
        return $output[0];
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM events WHERE eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    private function formatDateTimeToString(DateTime $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }

    public function createEvent($name, DateTime $startTime, DateTime $endTime, ?int $eventTypeId, ?int $availableTickets): int
    {
        if ($eventTypeId === null) {
            $eventTypeId = 'NULL';
        }
        if ($availableTickets === null) {
            $availableTickets = 'NULL';
        }
        $sql = "INSERT INTO Events (name, startTime, endTime, festivalEventType, availableTickets) VALUES (:name, :startTime, :endTime, :eventTypeId, :availableTickets)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $startToString = $this->formatDateTimeToString($startTime);
        $stmt->bindParam(':startTime', $startToString, PDO::PARAM_STR);
        $endToString = $this->formatDateTimeToString($endTime);
        $stmt->bindParam(':endTime', $endToString, PDO::PARAM_STR);
        $stmt->bindParam(':eventTypeId', $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':availableTickets', $availableTickets, PDO::PARAM_INT);
        $stmt->execute();

        return $this->connection->lastInsertId();
    }

    public function updateEvent($id, $name, $startTime, $endTime, ?int $eventTypeId, ?int $availableTickets)
    {
        if ($eventTypeId === null) {
            $eventTypeId = 'NULL';
        }

        if ($availableTickets === null) {
            $availableTickets = 'NULL';
        }

        $sql = "UPDATE Events
        SET name = :name, startTime = :startTime, endTime = :endTime,
        festivalEventType = :eventTypeId, availableTickets = :availableTickets
        WHERE eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':startTime', $startTime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindParam(':endTime', $endTime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindParam(':eventTypeId', $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':availableTickets', $availableTickets, PDO::PARAM_INT);
        $stmt->execute();
    }

    // JAZZ
    private function isInJazzEvents($id)
    {
        $sql = "SELECT eventId FROM jazzevents WHERE eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $arr = $stmt->fetchAll();
        return count($arr) > 0;
    }

    // HISTORY
    private function isInHistoryEvents($id)
    {
        $sql = "SELECT eventId FROM historyevents WHERE eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $arr = $stmt->fetchAll();
        return count($arr) > 0;
    }

    public function getAllJazzEvents($sort, array $filters)
    {
        $sql = "SELECT je.eventId, je.artistId, je.locationId, e.name, e.startTime, e.endTime, e.festivalEventType, t.ticketTypePrice, e.availableTickets - (select count(t2.eventId) from tickets t2 where t2.eventid = e.eventId) as availableTickets " .
            "FROM jazzevents je " .
            "JOIN events e ON e.eventId = je.eventId " .
            "JOIN cartitems c on e.eventId = c.eventId " .
            "join tickettypes t on c.ticketTypeId = t.ticketTypeId ";


        if (!empty($filters) && !(count($filters) === 1 && isset($filters['artist_kind']))) {
            // if only filter is artist_kind, skip
            $sql .= " WHERE ";
            $i = 0;
            if (isset($filters['artist_kind'])) {
                $i++;
            }

            foreach ($filters as $key => $filter) {
                switch ($key) {
                    case 'price_from':
                        $sql .= " t.ticketTypePrice >= :$key ";
                        break;
                    case 'price_to':
                        $sql .= " t.ticketTypePrice <= :$key ";
                        break;
                    case 'time_from':
                        $sql .= " HOUR(e.startTime) >= :$key ";
                        break;
                    case 'time_to':
                        $sql .= " HOUR(e.endTime) <= :$key ";
                        break;
                    case 'hide_no_seats':
                        // TODO: Hide events with no seats.
                        break;
                    case 'day':
                        $sql .= " DAY(e.startTime) = :$key ";
                        break;
                    case 'date':
                        $sql .= " DATE(e.startTime) = :$key ";
                        break;
                    case 'location':
                        $sql .= " je.locationId = :$key ";
                        break;
                    default:
                        // no filtering by default
                        $i++;
                        continue 2;
                }

                if ($i < count($filters) - 1) {
                    $sql .= " AND ";
                }
                $i++;
            }
        }

        switch ($sort) {
            case "time_desc":
                $sql .= " ORDER BY e.startTime DESC";
                break;
            case "price":
                $sql .= " ORDER BY t.ticketTypePrice";
                break;
            case "price_desc":
                $sql .= " ORDER BY t.ticketTypePrice DESC";
                break;
            default:
                $sql .= " ORDER BY e.startTime";
                break;
        }

        $stmt = $this->connection->prepare($sql);

        if (!(count($filters) === 1 && isset($filters['artist_kind']))) {
            foreach ($filters as $key => $filter) {
                if ($key == 'artist_kind') {
                    continue;
                }

                $pdoType = is_numeric($filter) ? PDO::PARAM_INT : PDO::PARAM_STR;
                if (str_starts_with($key, 'price')) {
                    $pdoType = PDO::PARAM_STR;
                }
                $stmt->bindValue(':' . $key, $filter, $pdoType);
            }
        }

        $stmt->execute();
        $arr = $stmt->fetchAll();
        return $this->buildJazzEvent($arr, $filters);
    }

    public function getJazzEventById($id)
    {
        $sql = "SELECT je.eventId, je.artistId, je.locationId, e.name, e.startTime, e.endTime, e.festivalEventType, e.availableTickets - (select count(t2.eventId) from tickets t2 where t2.eventid = e.eventId) as availableTickets  "
            . "FROM jazzevents je "
            . "JOIN events e ON e.eventId = je.eventId "
            . "WHERE je.eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $arr = $stmt->fetchAll();
        return $this->buildJazzEvent($arr)[0];
    }

    // Get History Event by ID
    public function getHistoryEventById($id)
    {
        try {
            $locationRep = new LocationRepository();
            $eventTypeRep = new EventTypeRepository();

            $sql = "SELECT he.eventId as eventId, he.locationId as locationId, e.name as name,
            e.startTime as startTime, e.endTime as endTime, g.guideId as guideId, e.availableTickets as availableTickets, e.festivalEventType
            FROM historyevents he
            JOIN events e ON e.eventId = he.eventId
            join guides g on g.guideId = he.guideId
            where he.eventId  = :id";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();


            $guide = $this->getGuideByID($result['guideId']);
            $location = $locationRep->getById($result['locationId']);
            $startTime = new DateTime($result['startTime']);
            $endTime = new DateTime($result['endTime']);

            $evenType = $eventTypeRep->getById($result['festivalEventType']);

            $historyEvent = new HistoryEvent($result['eventId'], $result['name'], $result['availableTickets'], $startTime, $endTime, $guide, $location, $evenType);
            return $historyEvent;
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function getGuideByID($id)
    {
        try {
            $query = "SELECT g.guideId, g.name as firstName, g.lastName , g.`language` , g.description  FROM guides g where guideId = :id";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            // fetch result as an object
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Guide');
            $guide = $stmt->fetch();

            if (!$guide) {
                throw new Exception("No guide found");
            }
            return $guide;
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    // Create the history event
    public function createHistoryEvent($eventId, $guideId, $locationId): int
    {
        $sql = "INSERT INTO historyevents (eventId, guideId, locationId) VALUES (:eventId, :guideId, :locationId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':guideId', $guideId);
        $stmt->bindParam(':locationId', $locationId);
        $stmt->execute();

        return $this->connection->lastInsertId();
    }

    public function createJazzEvent($eventId, $artistId, $locationId): int
    {
        $sql = "INSERT INTO jazzevents (eventId, artistId, locationId) VALUES (:eventId, :artistId, :locationId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':artistId', $artistId);
        $stmt->bindParam(':locationId', $locationId);
        $stmt->execute();

        return $this->connection->lastInsertId();
    }

    public function updateJazzEvent($eventId, $artistId, $locationId)
    {
        $sql = "UPDATE jazzevents SET artistId = :artistId, locationId = :locationId WHERE eventId = :eventId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':artistId', $artistId);
        $stmt->bindParam(':locationId', $locationId);
        $stmt->execute();
    }

    public function updateHistoryEvent($eventId, $guideId, $locationId)
    {
        $sql = "UPDATE historyevents SET guideId = :guideId, locationId = :locationId WHERE eventId = :eventId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':guideId', $guideId);
        $stmt->bindParam(':locationId', $locationId);
        $stmt->execute();
    }

    public function getJazzEventsForArtist($artistId)
    {
        $sql = "SELECT je.eventId, je.artistId, je.locationId, e.name, e.startTime, e.endTime, e.festivalEventType, e.availableTickets "
            . "FROM jazzevents je "
            . "JOIN events e ON e.eventId = je.eventId "
            . "WHERE artistId = :artistId";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['artistId' => $artistId]);
        $arr = $stmt->fetchAll();
        return $this->buildJazzEvent($arr);
    }

    public function getFestivalDates($filters): array
    {
        $sql = "SELECT DISTINCT DATE(startTime) as date FROM events ";

        if (isset($filters['eventType'])) {
            $sql .= " WHERE festivalEventType = :eventType ";
        }

        $sql .= " ORDER BY date ";

        $stmt = $this->connection->prepare($sql);

        if (isset($filters['eventType'])) {
            $stmt->bindValue(':eventType', $filters['eventType']);
        }

        $stmt->execute();
        $arr = $stmt->fetchAll();
        return array_map(fn ($date) => $date['date'], $arr);
    }

    public function getPasses(): array
    {
        // passes don't have availableTickets
        $sql = "SELECT e.eventId, e.name, e.startTime, e.endTime, e.festivalEventType
            FROM events e
            WHERE e.availableTickets = 0";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $arr = $stmt->fetchAll();
        return $this->buildEvent($arr);
    }
}
