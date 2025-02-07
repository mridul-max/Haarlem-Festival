<?php

require_once("TicketLinkRepository.php");

class PassTicketLinkRepository extends TicketLinkRepository
{
    public function getAll($sort = null, $filters = [])
    {
        // availableTickets is null if it's a pass
        $sql = "SELECT e.eventId,
		e.name as eventName,
		e.startTime,
		e.endTime,
		e.availableTickets,
		f.eventTypeId as eventTypeId,
		f.name as eventTypeName,
		f.VAT as evenTypeVat,
		t.ticketTypeId as ticketTypeId,
		t.ticketTypeName as ticketTypeName,
		t.ticketTypePrice as ticketTypePrice,
		t.nrOfPeople as ticketTypeNrOfPeople,
        c.ticketLinkId as ticketLinkId
        FROM events e
        JOIN ticketlinks c on e.eventId = c.eventId
        join tickettypes t on c.ticketTypeId = t.ticketTypeId
        join festivaleventtypes f on f.eventTypeId  = e.festivalEventType
        WHERE e.availableTickets = 0";

        if (!empty($filters)) {
            $sql .= " AND ";
            foreach ($filters as $key => $value) {
                switch ($key) {
                    case 'event_type':
                        $sql .= "e.festivalEventType = :$key ";
                        break;
                    default:
                        break;
                }

                if (next($filters)) {
                    $sql .= " AND ";
                }
            }
        }

        $stmt = $this->connection->prepare($sql);

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }

        $stmt->execute();
        $result = $stmt->fetchAll();
        return $this->build($result);
    }
}
