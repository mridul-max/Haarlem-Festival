<?php

require_once(__DIR__ . "/../services/OrderService.php");
require_once(__DIR__ . "/../services/CartService.php");
require_once(__DIR__ . "/../services/InvoiceService.php");

class OrderController
{
    private $orderService;
    private $cartService;
    private $invoiceService;
    private $ticketService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->cartService = new CartService();
        $this->invoiceService = new InvoiceService();
        $this->ticketService = new TicketService();
    }

    public function showShoppingCart()
    {
        $hasStuffInCart = false;
        $cartOrder = null;
        $shareMode = false;
        $isLoggedIn = isset($_SESSION['user']);

        $isCustomerOrVisitor = true;
        if ($isLoggedIn) {
            $user = unserialize($_SESSION['user']);
            $isCustomerOrVisitor = ($user->getUserType() == 3);
        }

        try {
            // http://localhost/shopping-cart?id=16
            // Check if "id" is set in the URL query string
            // if so, other user is trying to share their cart with you
            if (isset($_GET["id"])) {
                $cartOrder = $this->cartService->getCartByOrderId($_GET["id"]);
                $shareMode = true;
            } else {
                $cartOrder = $this->cartService->getCart();
                if ($cartOrder->getTotalItemCount() > 0) {
                    $hasStuffInCart = true;
                }
            }
        } catch (Throwable $e) {
            Logger::write($e);
            $cartOrder = null;
        }

        require('../views/payment-funnel/cart.php');
    }

    /**
     * Show the logged in customer's order history
     */
    public function showOrderHistory()
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $customer = unserialize($_SESSION['user']);

            $orders = $this->orderService->getOrderHistory($customer->getUserId());

            if ($orders == null) {
                throw new Exception("No orders found");
            }
            require_once('../views/payment-funnel/order-history.php');
        } catch (Throwable $e) {
            Logger::write($e);
        }
    }

    public function getOrdersToExport()
    {
        try {
            $orders = $this->orderService->getOrdersToExport(true);
            require_once('../views/admin/viewOrders.php');

            return $orders;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function downloadOrders()
    {
        try {
            return $this->orderService->downloadOrders();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function sendInvoiceOfOrder()
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $customer = unserialize($_SESSION['user']);

            $orders = $this->orderService->getOrderHistory($customer->getUserId());

            // Get the specific order id from the url to send the invoice of that order
            $orderId = $_GET['orderId'];

            $order = $this->orderService->getOrderById($orderId);

            if ($orders == null) {
                throw new Exception("No orders found");
            }

            $this->invoiceService->sendInvoiceEmail($order);

            // show an alert that the invoice has been sent
            echo "<script>alert('Invoice has been sent to your email!')</script>";

            require_once('../views/payment-funnel/order-history.php');
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function sendTicketOfOrder()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $customer = unserialize($_SESSION['user']);

        $orders = $this->orderService->getOrderHistory($customer->getUserId());

        // Get the specific order id from the url to send the ticket of that order
        $orderId = $_GET['orderId'];

        $order = $this->orderService->getOrderById($orderId);

        if ($orders == null) {
            throw new Exception("No orders found");
        }

        $this->ticketService->getAllTicketsAndSend($order);

        // show an alert that the ticket has been sent
        echo "<script>alert('Ticket has been sent to your email!')</script>";

        require_once('../views/payment-funnel/order-history.php');
    }
}
