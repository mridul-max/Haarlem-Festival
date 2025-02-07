<?php
require_once(__DIR__ . "/../repositories/Repository.php");
require_once(__DIR__ . "/../models/Exceptions/OrderNotFoundException.php");
require_once(__DIR__ . "/../models/Order.php");
require_once(__DIR__ . "/../models/Ticket/Ticket.php");
require_once(__DIR__ . "/../models/Event.php");
require_once(__DIR__ . "/../models/Address.php");
require_once(__DIR__ . "/../models/Customer.php");
require_once(__DIR__ . "/UserRepository.php");
require_once(__DIR__ . "/CustomerRepository.php");
require_once(__DIR__ . "/TicketLinkRepository.php");

/**
 * Repository for orders and orderitems
 * @author Joshua
 */
class OrderRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrderById($orderId): Order
    {
        $sql = "SELECT * FROM orders WHERE orderId = :orderId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderId", htmlspecialchars($orderId));
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result)
            throw new OrderNotFoundException();
        $order = $this->buildOrder($result);
        $order->setOrderItems($this->getOrderItemsByOrderId($orderId));

        return $order;
    }

    private function getOrderItemById($orderItemId): OrderItem
    {
        $sql = "select o.orderItemId, tl.ticketLinkId, e.name as eventName, tt.ticketTypeName as ticketName, e.startTime, tt.ticketTypePrice as fullTicketPrice, f.VAT, o.quantity
                from orderitems o
                join ticketlinks tl on tl.ticketLinkId = o.ticketLinkId
                join tickettypes tt on tt.ticketTypeId = tl.ticketTypeId
                join events e on e.eventId = tl.eventId
                join festivaleventtypes f on f.eventTypeId = e.festivalEventType
                where o.orderItemId = :orderItemId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderItemId", htmlspecialchars($orderItemId));
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->buildOrderItem($result);
    }

    public function getAllOrders($limit = null, $offset = null, $isPaid = null)
    {
        $sql = "SELECT * FROM orders";

        if ($isPaid != null) {
            $sql .= " WHERE isPaid = :isPaid";
        }
        if ($limit != null) {
            $sql .= " LIMIT :limit";
        }
        if ($offset != null) {
            $sql .= " OFFSET :offset";
        }

        $stmt = $this->connection->prepare($sql);

        if ($isPaid != null) {
            $stmt->bindValue(":isPaid", htmlspecialchars($isPaid));
        }
        if ($limit != null) {
            $stmt->bindValue(":limit", htmlspecialchars($limit), PDO::PARAM_INT);
        }
        if ($offset != null) {
            $stmt->bindValue(":offset", htmlspecialchars($offset), PDO::PARAM_INT);
        }
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $orders = array();

        foreach ($result as $row) {
            $order = $this->buildOrder($row);
            $order->setOrderItems($this->getOrderItemsByOrderId($order->getOrderId()));
            array_push($orders, $order);
        }

        return $orders;
    }

    public function getOrderItemsByOrderId($orderId): array
    {
        $sql = "select o.orderItemId, tl.ticketLinkId, e.name as eventName, tt.ticketTypeName as ticketName, e.startTime, tt.ticketTypePrice as fullTicketPrice, f.VAT, o.quantity
                from orderitems o
                join ticketlinks tl on tl.ticketLinkId = o.ticketLinkId
                join tickettypes tt on tt.ticketTypeId = tl.ticketTypeId
                join events e on e.eventId = tl.eventId
                join festivaleventtypes f on f.eventTypeId = e.festivalEventType
                where o.orderId = :orderId";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderId", htmlspecialchars($orderId));
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //Build order item array
        $orderItems = array();
        foreach ($result as $row) {
            $orderItem = $this->buildOrderItem($row);
            array_push($orderItems, $orderItem);
        }

        return $orderItems;
    }

    public function getCartOrderForCustomer(int $customerId): ?Order
    {
        $sql = "SELECT * FROM orders WHERE customerId = :customerId AND isPaid = 0";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":customerId", htmlspecialchars($customerId));
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result)
            return null;
        else {
            $order = $this->buildOrder($result);
            $order->setOrderItems($this->getOrderItemsByOrderId($order->getOrderId()));
            return $order;
        }
    }

    public function getOrdersToExport($isPaid = null, $customerId = null)
    {
        $sql = "select o.orderId, o.orderDate, u.firstName , u.lastName , u.email , e.name, t.ticketId, o.customerId, o.isPaid from orders o
        join users u on u.userId = o.customerId
        left join tickets t on t.orderId = t.ticketId
        left join events e on t.eventId = e.eventId ";

        if ($isPaid != null) {
            $sql .= " WHERE isPaid = " . ($isPaid == 'true' || $isPaid == 1 ? "1" : "0") . " ";
        }

        if ($customerId != null) {

            if ($isPaid != null) {
                $sql .= " AND ";
            } else {
                $sql .= " WHERE  ";
            }

            $sql .= " o.customerId = :customerId ";
        }

        $stmt = $this->connection->prepare($sql);

        if ($customerId != null) {
            $stmt->bindValue(":customerId", htmlspecialchars($customerId));
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orders = [];
        $customerRep = new CustomerRepository();
        foreach ($result as $row) {
            $order = new Order();
            $order->setOrderId($row['orderId']);
            $order->setOrderDate(DateTime::createFromFormat('Y-m-d H:i:s', $row['orderDate']));
            $orderItems = $this->getOrderItemsByOrderId($row['orderId']);
            $order->setOrderItems($orderItems);
            $order->setIsPaid($row['isPaid']);


            if ($row['customerId'] != null) {
                $customer = $customerRep->getById($row['customerId']);
                $order->setCustomer($customer);
            }

            array_push($orders, $order);
        }
        return $orders;
    }

    public function getOrderForInvoice($orderId)
    {
        $sql = "select o.orderDate, u.firstName , u.lastName , u.email , e.name, t.ticketId, o.customerId from orders o
        join users u on u.userId = o.customerId
        join tickets t on t.orderId = o.orderId
        join events e on t.eventId = e.eventId
        where o.orderId = :orderId ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderId", $orderId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $order = new Order();
        $order->setOrderId($orderId);
        $order->setOrderDate(DateTime::createFromFormat('Y-m-d H:i:s', $result['orderDate']));
        $orderItems = $this->getOrderItemsByOrderId($orderId);
        $order->setOrderItems($orderItems);

        $customerRep = new CustomerRepository();
        $customer = $customerRep->getById($result['customerId']);
        $order->setCustomer($customer);

        return $order;
    }

    public function getOrderHistory($customerId): array
    {
        $sql = "SELECT * FROM orders WHERE customerId=:customerId AND isPaid = 1";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":customerId", $customerId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orders = [];
        foreach ($result as $row) {
            $order = new Order();
            $order->setOrderId($row['orderId']);
            $order->setOrderDate(DateTime::createFromFormat('Y-m-d H:i:s', $row['orderDate']));
            $orderItems = $this->getOrderItemsByOrderId($row['orderId']);
            $order->setOrderItems($orderItems);

            $customerRep = new CustomerRepository();
            $customer = $customerRep->getById($row['customerId']);
            $order->setCustomer($customer);

            $orders[] = $order;
        }
        return $orders;
    }

    public function updateOrder(int $orderId, Order $order): Order
    {
        $sql = "UPDATE orders SET orderDate = :orderDate, customerId = :customerId, isPaid = :isPaid WHERE orderId = :orderId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderDate", htmlspecialchars($order->getOrderDateAsString()));
        $stmt->bindValue(":customerId", $order->getCustomer()->getUserId());
        $stmt->bindValue(":isPaid", htmlspecialchars($order->getIsPaid()), PDO::PARAM_BOOL);
        $stmt->bindValue(":orderId", htmlspecialchars($orderId));

        $stmt->execute();
        return $order;
    }

    public function updateOrderItem($orderItemId, $orderItem, $orderId = null): OrderItem
    {
        if (!$orderId) {
            $sql = "UPDATE orderitems SET ticketLinkId = :ticketLinkId, quantity = :quantity WHERE orderItemId = :orderItemId";
        } else {
            $sql = "UPDATE orderitems SET ticketLinkId = :ticketLinkId, quantity = :quantity, orderId = :orderId WHERE orderItemId = :orderItemId";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":ticketLinkId", htmlspecialchars($orderItem->getTicketLinkId()));
        $stmt->bindValue(":quantity", htmlspecialchars($orderItem->getQuantity()));
        $stmt->bindValue(":orderItemId", htmlspecialchars($orderItemId));
        if ($orderId) {
            $stmt->bindValue(":orderId", htmlspecialchars($orderId));
        }

        $stmt->execute();
        return $this->getOrderItemById($orderItemId);
    }

    //Insert a new order into the database
    public function insertOrder($order): Order
    {
        $sql = "INSERT INTO orders (orderDate, customerId, isPaid) VALUES (:orderDate, :customerId, 0)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderDate", htmlspecialchars($order->getOrderDateAsString()));

        if ($order->getCustomer() != null) {
            $customerId = $order->getCustomer()->getUserId();
            $stmt->bindValue(":customerId", htmlspecialchars($customerId));
        } else {
            $stmt->bindValue(":customerId", null);
        }
        $stmt->execute();
        $insertId = $this->connection->lastInsertId();

        //Also cleanse the old orders from the database
        $this->removeOldOrders();

        return $this->getOrderById($insertId);
    }

    public function insertOrderItem($orderItem, $orderId): OrderItem
    {
        $sql = "INSERT INTO orderitems (ticketLinkId, orderId, quantity) VALUES (:ticketLinkId, :orderId, :quantity)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":ticketLinkId", htmlspecialchars($orderItem->getTicketLinkId()));
        $stmt->bindValue(":orderId", htmlspecialchars($orderId));
        $stmt->bindValue(":quantity", htmlspecialchars($orderItem->getQuantity()));
        $stmt->execute();

        return $this->getOrderItemById($this->connection->lastInsertId());
    }

    public function deleteOrder(int $orderId)
    {
        //Deletes one order. BEWARE: CASCADES, so it deletes ALL LINKED ORDERITEMS as well.
        $sql = "DELETE FROM orders WHERE orderId = :orderId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderId", $orderId);
    }

    public function deleteOrderItem(int $orderItemId)
    {
        //Deletes one order item.
        $sql = "DELETE FROM orderitems WHERE orderItemId = :orderItemId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":orderItemId", $orderItemId);
        $stmt->execute();
    }


    //This method is used to remove orders that were never linked to an account and that are 2 days old.
    private function removeOldOrders()
    {
        $sql = "DELETE
                FROM orders
                WHERE customerId IS NULL
                AND orderDate < DATE_SUB(NOW(), INTERVAL 2 DAY)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
    }

    private function buildOrder($row): Order
    {
        $order = new Order();
        $order->setOrderId($row['orderId']);
        $order->setOrderDate(DateTime::createFromFormat('Y-m-d H:i:s', $row['orderDate']));

        if ($row['customerId'] != null) {
            $order->setCustomer(new Customer());
            $order->getCustomer()->setUserId($row['customerId']);
        } else {
            $order->setCustomer(null);
        }
        $order->setIsPaid($row['isPaid']);

        return $order;
    }

    private function buildOrderItem($row): OrderItem
    {
        $orderItem = new OrderItem();
        $orderItem->setOrderItemId($row['orderItemId']);
        $orderItem->setTicketLinkId($row['ticketLinkId']);
        $orderItem->setTicketName($row['ticketName']);
        $orderItem->setVatPercentage($row['VAT']);
        $orderItem->setFullTicketPrice($row['fullTicketPrice']);
        $orderItem->setQuantity($row['quantity']);
        $orderItem->setEventName($this->formatEventName($row['eventName'], $row['startTime']));

        return $orderItem;
    }

    private function formatEventName($eventName, $startTime)
    {
        $date = date_create($startTime);
        $formattedDate = date_format($date, 'd-m-Y H:i');
        return $eventName . " - " . $formattedDate;
    }
}
