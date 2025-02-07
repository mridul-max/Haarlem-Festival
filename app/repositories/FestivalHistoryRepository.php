<?php

require_once("../models/History/HistoryEvent.php");
require_once("../repositories/Repository.php");

require_once("../models/Guide.php");
require_once("../models/Location.php");
require_once("../models/Address.php");

require_once("AddressRepository.php");
require_once("LocationRepository.php");
require_once("EventTypeRepository.php");

class FestivalHistoryRepository extends Repository
{
    private $eventTypeRepository;
    private $locationRepository;

    public function __construct()
    {
        parent::__construct();
        $this->locationRepository = new LocationRepository();
        $this->eventTypeRepository = new EventTypeRepository();
    }
    public function getAllHistoryEvents()
    {
        try {
            $query = "SELECT he.eventId as eventId, he.locationId as locationId, e.name as name,
            e.startTime as startTime, e.endTime as endTime, g.guideId as guideId, e.availableTickets as availableTickets, e.festivalEventType
            FROM historyevents he
            JOIN events e ON e.eventId = he.eventId
                    join ticketlinks t on e.eventId = t.eventId 
                    join guides g ON g.guideId = he.guideId
                    join locations l ON he.locationId = l.locationId
                    join festivaleventtypes f on f.eventTypeId = e.festivalEventType";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            $historyEvents = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $historyEvent = new HistoryEvent(
                    $row['eventId'],
                    $row['name'],
                    $row['availableTickets'],
                    new DateTime($row['startTime']),
                    new DateTime($row['endTime']),
                    $this->getGuideByID($row['guideId']),
                    $this->locationRepository->getById($row['locationId']),
                    $this->eventTypeRepository->getById($row['festivalEventType'])
                );
                $historyEvents[] = $historyEvent;
            }

            return $historyEvents;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getById($id){
        try{
            $query = "SELECT he.eventId as eventId, he.locationId as locationId, e.name as name,
            e.startTime as startTime, e.endTime as endTime, g.guideId as guideId, e.availableTickets as availableTickets, e.festivalEventType
            FROM historyevents he
            JOIN events e ON e.eventId = he.eventId
                    join ticketlinks t on e.eventId = t.eventId 
                    join guides g ON g.guideId = he.guideId
                    join locations l ON he.locationId = l.locationId
                    join festivaleventtypes f on f.eventTypeId = e.festivalEventType
                    where he.eventId = :id";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $historyEvent = new HistoryEvent(
                $row['eventId'],
                $row['name'],
                $row['availableTickets'],
                new DateTime($row['startTime']),
                new DateTime($row['endTime']),
                $this->getGuideByID($row['guideId']),
                $this->locationRepository->getById($row['locationId']),
                $this->eventTypeRepository->getById($row['festivalEventType'])
            );

            return $historyEvent;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getAllGuides(){
        try {
            $query = "SELECT g.guideId, g.name as firstName, g.lastName , g.`language`  FROM guides g";

            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            // fetch each results as objects
            $guides = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $guide = new Guide();
                $guide->setGuideId($row['guideId']);
                $guide->setFirstName($row['firstName']);
                $guide->setLastName($row['lastName']);
                $guide->setLanguage($row['language']);
                
                $guides[] = $guide;
            }

            return $guides;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // TODO: remove this method
    public function getGuideByID($id)
    {
        try {
            $query = "SELECT g.guideId, g.name as firstName, g.lastName , g.`language`  FROM guides g where guideId = :id";

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
}