<?php

require_once(__DIR__ . "/../services/OrderService.php");
require_once(__DIR__ . "/../services/CartService.php");

class OrderController
{
    private $orderService;
    private $cartService;
    private $ticketService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->cartService = new CartService();
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
            $cartOrder = null;
        }

        require('../views/payment-funnel/cart.php');
    }

    public function showOrderHistory()
    {
    
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $customer = unserialize($_SESSION['user']);

            $orders = $this->orderService->getOrderHistory($customer->getUserId());

            if ($orders == null) {
                throw new Exception("No orders found");
            }
            require_once('../views/payment-funnel/order-history.php');
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

    public function sendTicketOfOrder()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $customer = unserialize($_SESSION['user']);

        $orders = $this->orderService->getOrderHistory($customer->getUserId());

        $orderId = $_GET['orderId'];

        $order = $this->orderService->getOrderById($orderId);

        if ($orders == null) {
            throw new Exception("No orders found");
        }

        $this->ticketService->getAllTicketsAndSend($order);

        echo "<script>alert('Ticket has been sent to your email!')</script>";

        require_once('../views/payment-funnel/order-history.php');
    }
}
