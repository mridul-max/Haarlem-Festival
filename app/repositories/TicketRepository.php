<?php
require_once(__DIR__ . '/../models/Ticket/Ticket.php');
require_once(__DIR__ . '/../models/Event.php');
require_once(__DIR__ . '/../models/Address.php');
require_once(__DIR__ . '/../models/Customer.php');
require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../models/Guide.php');
require_once(__DIR__ . '/../models/Order.php');

require_once(__DIR__ . '/../repositories/Repository.php');
require_once(__DIR__ . '/../models/Exceptions/TicketNotFoundException.php');

require_once(__DIR__ . '/../repositories/EventRepository.php');
require_once(__DIR__ . '/../repositories/UserRepository.php');


class TicketRepository extends Repository
{

    // Add ticket to database
    public function insertTicket($orderId, OrderItem $orderItem, Event $event, $ticketTypeId): Ticket
    {
        $query = "INSERT INTO tickets (eventId, isScanned, orderId, basePrice, vat, fullPrice, ticketTypeId) VALUES (:eventId, :isScanned, :orderId, :basePrice, :vat, :fullPrice, :ticketTypeId)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(":eventId", $event->getId());
        $stmt->bindValue(":isScanned", 0);
        $stmt->bindValue(":orderId", $orderId);
        $stmt->bindValue(":basePrice", $orderItem->getBasePrice());
        $stmt->bindValue(":vat", $event->getVat());
        $stmt->bindValue(":fullPrice", $orderItem->getFullTicketPrice());
        $stmt->bindValue(":ticketTypeId", $ticketTypeId);

        $stmt->execute();

        $ticketId = $this->connection->lastInsertId();

        $ticket = new Ticket();
        $ticket->setTicketId($ticketId);

        return $ticket;
    }
    public function getAllTicketsByOrderIdAndEventType(Order $order, string $eventType)
    {
        $eventType = strtolower($eventType);

        switch ($eventType) {
            case 'history':
                $eventTable = 'historyevents';
                break;
            case 'jazz':
                $eventTable = 'jazzevents';
                break;
            case 'yummy':
                $eventTable = 'yummyevents';
                break;
            case 'dance':
                $eventTable = 'danceevents';
                break;
            default:
                throw new InvalidArgumentException('Invalid event type');
        }

        $sql = "SELECT t.ticketId, t.eventId, t.isScanned, SUM(t2.ticketTypePrice) AS price, c.userId, l.name AS locationName
            FROM tickets t
            JOIN orders o ON o.orderId = t.orderId
            JOIN events e ON t.eventId = e.eventId
            JOIN festivaleventtypes f ON e.festivalEventType = f.eventTypeId
            JOIN customers c ON o.customerId = c.userId
            JOIN tickettypes t2 ON t.ticketTypeId = t2.ticketTypeId
            JOIN $eventTable h ON e.eventId = h.eventId
            JOIN locations l ON h.locationId = l.locationId
            WHERE t.orderId = :orderId
            GROUP BY t.ticketId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderId", $order->getOrderId());
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $tickets = [];

        foreach ($result as $row) {
            $ticket = $this->getTicketById($row['ticketId']);
            $ticket->setTicketId($row['ticketId']);
            $ticket->setFullPrice($row['price']);

            $userRep = new UserRepository();
            $user = $userRep->getById($row['userId']);
            $customer = new Customer();
            $customer->setFirstName($user->getFirstName());
            $customer->setLastName($user->getLastName());
            $customer->setEmail($user->getEmail());
            $order->setCustomer($customer);

            $eventRep = new EventRepository();
            $event = $eventRep->getEventById($row['eventId']);
            $ticket->setEvent($event);

            array_push($tickets, $ticket);
        }

        return $tickets;
    }

    public function getAllDayTicketsForPasses(Order $order)
    {

        $sql = "SELECT t.ticketId, t.eventId, t.isScanned, SUM(t2.ticketTypePrice) AS price, c.userId
            FROM tickets t
            JOIN orders o ON o.orderId = t.orderId
            JOIN events e ON t.eventId = e.eventId
            JOIN festivaleventtypes f ON e.festivalEventType = f.eventTypeId
            JOIN customers c ON o.customerId = c.userId
            JOIN tickettypes t2 ON t.ticketTypeId = t2.ticketTypeId
            WHERE t.orderId = :orderId
                AND e.availableTickets = 0
            GROUP BY t.ticketId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderId", $order->getOrderId());
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $tickets = [];

        foreach ($result as $row) {
            $ticket = $this->getTicketById($row['ticketId']);
            $ticket->setTicketId($row['ticketId']);
            $ticket->setFullPrice($row['price']);

            $userRep = new UserRepository();
            $user = $userRep->getById($row['userId']);
            $customer = new Customer();
            $customer->setFirstName($user->getFirstName());
            $customer->setLastName($user->getLastName());
            $customer->setEmail($user->getEmail());
            $order->setCustomer($customer);

            $eventRep = new EventRepository();
            $event = $eventRep->getEventById($row['eventId']);
            $ticket->setEvent($event);

            array_push($tickets, $ticket);
        }

        return $tickets;
    }

    public function getAllYummyTicketsByOrderId(Order $order)
    {

        $sql = "SELECT t.ticketId, t.eventId, t.isScanned, SUM(t2.ticketTypePrice) AS ticketPrice, c.userId, l.name AS locationName
            FROM tickets t
            JOIN orders o ON o.orderId = t.orderId
            JOIN events e ON t.eventId = e.eventId
            JOIN festivaleventtypes f ON e.festivalEventType = f.eventTypeId
            JOIN customers c ON o.customerId = c.userId
            JOIN tickettypes t2 ON t.ticketTypeId = t2.ticketTypeId
            JOIN restaurantevent h ON e.eventId = h.eventId
            JOIN restaurants r ON h.restaurantId = r.restaurantId
            JOIN locations l ON r.addressId = l.addressId
            WHERE t.orderid = :orderId
            GROUP BY t.ticketId, t.eventId, t.isScanned, c.userId, locationName";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderId", $order->getOrderId());
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $tickets = [];

        foreach ($result as $row) {
            $ticket = $this->getTicketById($row['ticketId']);
            $ticket->setTicketId($row['ticketId']);
            $ticket->setFullPrice($row['ticketPrice']);

            $userRep = new UserRepository();
            $user = $userRep->getById($row['userId']);
            $customer = new Customer();
            $customer->setFirstName($user->getFirstName());
            $customer->setLastName($user->getLastName());
            $customer->setEmail($user->getEmail());
            $order->setCustomer($customer);

            require_once("RestaurantRepository.php");

            $eventRep = new RestaurantRepository();
            $ticketLink = $eventRep->getByEventId($row['eventId']);
            $ticket->setEvent($ticketLink->getEvent());

            array_push($tickets, $ticket);
        }

        return $tickets;
    }


    public function markTicketAsScanned(Ticket $ticket)
    {
        try {
            $ticketId = $ticket->getTicketId();
            $sql = "UPDATE tickets SET isScanned = 1 WHERE ticketId = :ticketId";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(":ticketId", $ticketId);
            $stmt->execute();
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function getTicketById($ticketId)
    {
        try {
            $sql = "SELECT * FROM tickets WHERE ticketId = :ticketId";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(":ticketId", $ticketId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (is_bool($result)) {
                throw new TicketNotFoundException("Ticket ID not found");
            }

            $ticket = new Ticket();
            $ticket->setTicketId($result['ticketId']);
            $ticket->setIsScanned($result['isScanned']);
            $ticket->setBasePrice($result['basePrice']);
            $ticket->setVat($result['vat']);

            return $ticket;
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function addTicketToOrder($orderId, $ticket)
    {
    }

    public function removeTicketFromOrder($orderId, $ticket)
    {
    }
}
