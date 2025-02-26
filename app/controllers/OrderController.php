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
        $isLoggedIn = isset($_SESSION['user']);

        $isCustomerOrVisitor = true;
        if ($isLoggedIn) {
            $user = unserialize($_SESSION['user']);
            $isCustomerOrVisitor = ($user->getUserType() == 3);
        }

        try {
            $cartOrder = $this->cartService->getCart();
            if ($cartOrder->getTotalItemCount() > 0) {
                $hasStuffInCart = true;
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
    
        try {
            $customer = unserialize($_SESSION['user']);
            
            if (!$customer) {
                throw new Exception("User not logged in.");
            }
    
            $orders = $this->orderService->getOrderHistory($customer->getUserId());
    
            if (empty($orders)) {
                throw new Exception("No orders found.");
            }
    
            require_once('../views/payment-funnel/order-history.php');
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            require_once('../views/payment-funnel/order-history.php');
        }
    }
    
}
