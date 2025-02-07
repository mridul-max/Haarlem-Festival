<?php
//Repositories
require_once(__DIR__ . '/../repositories/OrderRepository.php');
require_once(__DIR__ . '/../repositories/CustomerRepository.php');

//Services
require_once(__DIR__ . '/../services/TicketService.php');
require_once(__DIR__ . '/../services/InvoiceService.php');
require_once(__DIR__ . '/../services/TicketLinkService.php');

//Models
require_once(__DIR__ . '/../models/Order.php');
require_once(__DIR__ . '/../models/Exceptions/OrderNotFoundException.php');

class OrderService
{
    private $orderRepository;
    private $customerRepository;
    private $invoiceService;
    private $ticketService;
    private $ticketLinkService;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->customerRepository = new CustomerRepository();
        $this->invoiceService = new InvoiceService();
        $this->ticketService = new TicketService();
        $this->ticketLinkService = new TicketLinkService();
    }

    public function getOrderById(int $id): Order
    {
        //Get the order object
        $order = $this->orderRepository->getOrderById($id);
        //Get the customer object attached in order
        //Customer may be null if the order is made by a visitor
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

    public function getOrdersToExport($isPaid = null, $customerId = null)
    {
        return $this->orderRepository->getOrdersToExport($isPaid, $customerId);
    }

    public function downloadOrders()
    {
        $orders = $this->getOrdersToExport(true);

        if ($orders == null) {
            echo "No orders found";
            exit;
        }

        $fileName = "orders-data_" . date('Y-m-d') . ".xls";
        $fields = array('ID', 'ORDER DATE', 'CUSTOMER NAME', 'CUSTOMER EMAIL', 'EVENT NAME', 'BASE PRICE', 'PRICE', 'QUANTITY', 'TOTAL BASE PRICE', 'TOTAL PRICE');
        $excelData = implode("\t", $fields) . "\n";

        foreach ($orders as $order) {
            foreach ($order->getOrderItems() as $orderItem) {
                $lineData = array(
                    $order->getOrderId(),
                    date_format($order->getOrderDate(), 'd/m/Y'),
                    $order->getCustomer()->getFirstName() . " " . $order->getCustomer()->getLastName(),
                    $order->getCustomer()->getEmail(),
                    $orderItem->getEventName(),
                    number_format($orderItem->getBasePrice(), 2),
                    number_format($orderItem->getFullTicketPrice(), 2),
                    $orderItem->getQuantity(),
                    number_format($orderItem->getTotalBasePrice(), 2),
                    number_format($orderItem->getTotalFullPrice(), 2)
                );
                array_walk($lineData, array($this, 'filterData'));
                $excelData .= implode("\t", $lineData) . "\n";
            }
        }

        // Send HTTP headers
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Cache-Control: max-age=0");

        // Output the Excel data to the output buffer and exit
        echo $excelData;
        exit;
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

    //If the customer has an unpaid order and logs in while having created another order as a visitor, merge the two orders.
    public function mergeOrders(Order $customerOrder, Order $sessionOrder): Order
    {
        //Nested loop that checks if there are order items that represent the same ticket
        foreach ($customerOrder->getOrderItems() as $customerOrderItem) {
            foreach ($sessionOrder->getOrderItems() as $sessionOrderItem) {

                if ($sessionOrderItem->getTicketLinkId() == $customerOrderItem->getTicketLinkId()) {
                    //If there is a match in ticket link then add the quantity of the sessionOrderItem to the customerOrderItem and update
                    $customerOrderItem->setQuantity($customerOrderItem->getQuantity() + $sessionOrderItem->getQuantity());
                    $this->updateOrderItem($customerOrderItem->getOrderItemId(), $customerOrderItem);
                } else {
                    //If the orderItem is unique then we add it to the customerOrder and update it
                    $this->orderRepository->updateOrderItem($sessionOrderItem->getOrderItemId(), $sessionOrderItem, $customerOrder->getOrderId());
                    $customerOrder->addOrderItem($sessionOrderItem);
                }
            }
        }

        //Delete the sessionOrder from db
        $this->deleteOrder($sessionOrder->getOrderId());

        return $customerOrder;
    }

    /**
     * After checking out the cart, this function will be called to send the tickets and invoice to the user.
     * @param Order $order
     * @return void
     * @throws Exception
     */
    public function sendTicketsAndInvoice(Order $order): void
    {
        // Clone object of $order to prevent the original object from being modified
        $orderClone = clone $order;

        // Generate the tickets for the order
        $this->generateTicketsForOrder($order);
        //Send all the tickets via email
        $this->ticketService->getAllTicketsAndSend($orderClone);
        //Send invoice via email
        $this->invoiceService->sendInvoiceEmail($order);
    }

    /**
     * Generates tickets for the order
     */
    private function generateTicketsForOrder(Order $order)
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $ticketLink = $this->ticketLinkService->getById($orderItem->getTicketLinkId());

            for ($i = 0; $i < $orderItem->getQuantity(); $i++) {
                $this->ticketService->insertTicket($order->getOrderId(), $orderItem, $ticketLink->getEvent(), $ticketLink->getTicketType()->getId());
            }
        }
    }
}
