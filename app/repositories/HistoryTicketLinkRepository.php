<?php

require_once("EventRepository.php");
require_once("TicketLinkRepository.php");
require_once("TicketTypeRepository.php");

class HistoryTicketLinkRepository extends TicketLinkRepository
{
    protected function build($arr): array
    {
        $output = array();

        foreach ($arr as $item) {
            $guide = new Guide();
            $guide->setGuideId($item['guideId']);
            $guide->setFirstName($item['name']);
            $guide->setLastName($item['lastName']);
            $guide->setLanguage($item['language']);

            $eventType = new EventType(
                $item['eventTypeId'],
                $item['eventTypeName'],
                $item['VAT']
            );

            $location = new Location();
            $location->setLocationId($item['locationId']);
            $location->setName($item['locationName']);
            $location->setLocationType(3);
            $location->setCapacity($item['capacity']);
            $address = new Address();
            $address->setAddressId($item['addressId']);
            $address->setStreetName($item['streetName']);
            $address->setHouseNumber($item['houseNumber']);
            $address->setPostalCode($item['postalCode']);
            $address->setCity($item['city']);
            $address->setCountry($item['country']);
            $location->setAddress($address);
            $location->setDescription($item['description']);
            $location->setLat($item['lat']);
            $location->setLon($item['lon']);


            $event = new HistoryEvent(
                $item['eventId'],
                $item['eventName'],
                $item['availableTickets'],
                new DateTime($item['startTime']),
                new DateTime($item['endTime']),
                $guide,
                $location,
                $eventType
            );
            $event->setAvailableTickets($this->calculateAvailableTickets($event));
            $event->setEventType($eventType);

            $ticketType = new TicketType(
                $item['ticketTypeId'],
                $item['ticketTypeName'],
                $item['ticketTypePrice'],
                $item['nrOfPeople']
            );

            $cartItem = new TicketLink($item['ticketLinkId'], $event, $ticketType);
            array_push($output, $cartItem);
        }

        return $output;
    }

    public function getAll($sort = null, $filters = [])
    {
        try {
            $sql = "select c.ticketLinkId,
             e.eventId,
             e.name as eventName,
             e.startTime,
             e.endTime,
             e.eventId,
             e.festivalEventType,
             e.availableTickets,
             t.ticketTypeId,
             h.locationId,
             t.ticketTypeId,
             t.ticketTypeName,
             t.ticketTypePrice,
             t.nrOfPeople,
             f.eventTypeId,
             f.name as eventTypeName,
             f.VAT,
             l.name as locationName,
             l.locationType,
             l.lon,
             l.lat,
             l.description,
             l.capacity,
             a.addressId,
             a.streetName,
             a.houseNumber,
             a.postalCode,
             a.city,
             a.country,
             g.guideId,
             g.name,
             g.lastName,
             g.`language`
             from ticketlinks c
             join tickettypes t ON t.ticketTypeId = c.ticketTypeId
             join events e  on e.eventId = c.eventId
             join historyevents h on h.eventId  = e.eventId
             join guides g on g.guideId = h.guideId
             join locations l on l.locationId = h.locationId
             join festivaleventtypes f on f.eventTypeId = e.festivalEventType
             join addresses a on a.addressId = l.addressId ";

            if (!empty($filters)) {
                $sql .= " WHERE ";

                foreach ($filters as $key => $value) {
                    switch ($key) {
                        case "date":
                            // date equals.
                            $sql .= " DATE(e.startTime) = :$key ";
                            break;
                        case "language":
                            $sql .= " g.language = :$key ";
                            break;
                        case "time":
                            $sql .= " hour(e.startTime) = :$key ";
                            break;
                        case "type":
                            $sql .= " c.ticketTypeId = :$key ";
                            break;
                    }

                    if (next($filters)) {
                        $sql .= " AND ";
                    }
                }
            }

            $sql .= " ORDER BY e.startTime ASC";

            $stmt = $this->connection->prepare($sql);

            foreach ($filters as $key => $value) {
                if ($key === 'time') {
                    // split at : and get first part
                    $value = explode(':', $value)[0];
                }

                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            $result = $stmt->fetchAll();

            return $this->build($result);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getById($id) : ?TicketLink {

        $sql = "select c.ticketLinkId,
             e.eventId,
             e.name as eventName,
             e.startTime,
             e.endTime,
             e.eventId,
             e.festivalEventType,
             e.availableTickets,
             t.ticketTypeId,
             h.locationId,
             t.ticketTypeId,
             t.ticketTypeName,
             t.ticketTypePrice,
             t.nrOfPeople,
             f.eventTypeId,
             f.name as eventtypename,
             f.VAT,
             l.name as locationname,
             l.locationType,
             l.lon,
             l.lat,
             l.description,
             l.capacity,
             a.addressId,
             a.streetName,
             a.houseNumber,
             a.postalCode,
             a.city,
             a.country,
             g.guideId,
             g.name,
             g.lastName,
             g.`language`
             from ticketlinks c
             join tickettypes t ON t.ticketTypeId = c.ticketTypeId
             join events e  on e.eventId = c.eventId
             join historyevents h on h.eventId  = e.eventId
             join guides g on g.guideId = h.guideId
             join locations l on l.locationId = h.locationId
             join festivaleventtypes f on f.eventTypeId = e.festivalEventType
             join addresses a on a.addressId = l.addressId
             WHERE c.ticketLinkId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $this->build([$result])[0];
    }

    public function getByEventId($id): ?TicketLink
    {
        $sql = "select c.ticketLinkId,
             e.eventId,
             e.name as eventName,
             e.startTime,
             e.endTime,
             e.eventId,
             e.festivalEventType,
             e.availableTickets,
             t.ticketTypeId,
             h.locationId,
             t.ticketTypeId,
             t.ticketTypeName,
             t.ticketTypePrice,
             t.nrOfPeople,
             f.eventTypeId,
             f.name as eventtypename,
             f.VAT,
             l.name as locationname,
             l.locationType,
             l.lon,
             l.lat,
             l.description,
             l.capacity,
             a.addressId,
             a.streetName,
             a.houseNumber,
             a.postalCode,
             a.city,
             a.country,
             g.guideId,
             g.name,
             g.lastName,
             g.`language`
             from ticketlinks c
             join tickettypes t ON t.ticketTypeId = c.ticketTypeId
             join events e  on e.eventId = c.eventId
             join historyevents h on h.eventId  = e.eventId
             join guides g on g.guideId = h.guideId
             join locations l on l.locationId = h.locationId
             join festivaleventtypes f on f.eventTypeId = e.festivalEventType
             join addresses a on a.addressId = l.addressId
             WHERE e.eventId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $output = $this->build($result);
        if (count($output) > 0) {
            return $output[0];
        }
        return null;
    }

    private function calculateAvailableTickets(HistoryEvent $event) : int {
        $sql = "SELECT count(nrOfPeople) as soldTickets
                FROM tickets t 
                JOIN tickettypes t2 on t.ticketTypeId = t2.ticketTypeId 
                WHERE eventId = :eventId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':eventId', $event->getId());

        $stmt->execute();
        $result = $stmt->fetch();
        $soldTickets = $result['soldTickets'];

        return $event->getAvailableTickets() - $soldTickets;
    }
}
