<?php
//Repositories
require_once(__DIR__ . '/../repositories/OrderRepository.php');
require_once(__DIR__ . '/../repositories/CustomerRepository.php');
require_once(__DIR__ . '/../services/TicketService.php');
require_once(__DIR__ . '/../services/TicketLinkService.php');

//Models
require_once(__DIR__ . '/../models/Order.php');
require_once(__DIR__ . '/../models/Exceptions/OrderNotFoundException.php');

class OrderService
{
    private $orderRepository;
    private $customerRepository;
    private $ticketService;
    private $ticketLinkService;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->customerRepository = new CustomerRepository();
        $this->ticketService = new TicketService();
        $this->ticketLinkService = new TicketLinkService();
    }

    public function getOrderById(int $id): Order
    {
        $order = $this->orderRepository->getOrderById($id);
        if ($order->getCustomer() != null) {
            $order->setCustomer($this->customerRepository->getById($order->getCustomer()->getUserId()));
        } else {
            $order->setCustomer(null);
        }
        return $order;
    }

    public function getOrderHistory(int $customerId): array
    {
        return $this->orderRepository->getOrderHistory($customerId);
    }

    private function filterData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    public function getCartOrderForCustomer($customerId)
    {
        return $this->orderRepository->getCartOrderForCustomer($customerId);
    }

    public function createOrder($ticketLinkId, $customerId = NULL): Order
    {
        $order = new Order();
        $order->setOrderDate(new DateTime());
        $order->setIsPaid(false);

        if (isset($customerId))
            $order->setCustomer($this->customerRepository->getById($customerId));

        $order = $this->orderRepository->insertOrder($order);

        //After we created the order, we can create the first orderItem that will be linked to the new order.
        $this->createOrderItem($ticketLinkId, $order->getOrderId());
        return $order;
    }

    public function createOrderItem(int $ticketLinkId, int $orderId): OrderItem
    {
        $orderItem = new OrderItem();
        $orderItem->setTicketLinkId($ticketLinkId);
        $orderItem->setQuantity(1);

        return $this->orderRepository->insertOrderItem($orderItem, $orderId);
    }

    public function updateOrder(int $orderId, Order $order): Order
    {
        return $this->orderRepository->updateOrder($orderId, $order);
    }

    public function updateOrderItem($orderItemId, $orderItem, $orderId = null): OrderItem
    {
        return $this->orderRepository->updateOrderItem($orderItemId, $orderItem, $orderId);
    }

    public function deleteOrder($orderId): void
    {
        $this->orderRepository->deleteOrder($orderId);
    }

    public function deleteOrderItem($orderItemId): void
    {
        $this->orderRepository->deleteOrderItem($orderItemId);
    }

    public function mergeOrders(Order $customerOrder, Order $sessionOrder): Order
    {
        foreach ($customerOrder->getOrderItems() as $customerOrderItem) {
            foreach ($sessionOrder->getOrderItems() as $sessionOrderItem) {

                if ($sessionOrderItem->getTicketLinkId() == $customerOrderItem->getTicketLinkId()) {
                    $customerOrderItem->setQuantity($customerOrderItem->getQuantity() + $sessionOrderItem->getQuantity());
                    $this->updateOrderItem($customerOrderItem->getOrderItemId(), $customerOrderItem);
                } else {
                    $this->orderRepository->updateOrderItem($sessionOrderItem->getOrderItemId(), $sessionOrderItem, $customerOrder->getOrderId());
                    $customerOrder->addOrderItem($sessionOrderItem);
                }
            }
        }
        $this->deleteOrder($sessionOrder->getOrderId());

        return $customerOrder;
    }
}
