<?php

require_once(__DIR__ . '/../models/Exceptions/EventSoldOutException.php');
require_once(__DIR__ . '/../models/Exceptions/CartException.php');
require_once(__DIR__ . '/../models/Exceptions/AuthenticationException.php');
require_once('OrderService.php');
require_once('CustomerService.php');

class CartService
{
    private $orderService;
    private $customerService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->customerService = new CustomerService();
    }

    private function cartIsInitialised(): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return (isset($_SESSION["cartId"]));
    }

    private function initialiseCart($ticketLinkId): Order
    {
        if ($this->cartIsInitialised()) {
            throw new CartException("Cart is already initialised.");
        }

        if (isset($_SESSION["user"])) {
            $user = unserialize($_SESSION["user"]);

            if ($user->getUserTypeAsString() == "Customer") {
                $customerId = $user->getUserId();
            } else {
                throw new Exception("User is not a customer.");
            }

            $order = $this->orderService->createOrder($ticketLinkId, $customerId);
            $realUser = $this->customerService->getCustomerById($customerId);
            $order->setCustomer($realUser);
        } else
            $order = $this->orderService->createOrder($ticketLinkId);

        $_SESSION["cartId"] = $order->getOrderId();
        return $order;
    }
 
    public function getCart(): Order
    {
        if (!$this->cartIsInitialised()) {
            throw new CartException("Cart is not initialised.");
        }

        $orderId = $_SESSION["cartId"];
        return $this->orderService->getOrderById($orderId);
    }

    public function getCount(): int
    {
        if (!$this->cartIsInitialised()) {
            return 0;
        } else {
            $orderId = $_SESSION["cartId"];
            $order = $this->orderService->getOrderById($orderId);
            return $order->getTotalItemCount();
        }
    }

    public function getCartByOrderId($orderId): Order
    {
        return $this->orderService->getOrderById($orderId);
    }

    public function addItem($ticketLinkId): Order
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["cartId"])) {
            $order = $this->initialiseCart($ticketLinkId);
            $_SESSION["cartId"] = $order->getOrderId();
            return $order;
        } else {
            $orderId = $_SESSION["cartId"];
            $order = $this->orderService->getOrderById($orderId);

            // Check if the item exists in the order
            $existingItem = null;
            foreach ($order->getOrderItems() as $item) {
                if ($item->getTicketLinkId() == $ticketLinkId) {
                    $existingItem = $item;
                    break;
                }
            }

            if ($existingItem) {
                // Increment quantity
                $existingItem->setQuantity($existingItem->getQuantity() + 1);
                $this->orderService->updateOrderItem($existingItem->getOrderItemId(), $existingItem);
            } else {
                // Add new OrderItem
                $this->orderService->createOrderItem($ticketLinkId, $orderId);
            }

            // Reload the order to reflect changes
            $order = $this->orderService->getOrderById($orderId);
            return $order;
        }
    }

    public function removeItem($ticketLinkId): Order
    {
        $order = $this->getCart();

        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getTicketLinkId() == $ticketLinkId) {
                $orderItem->setQuantity($orderItem->getQuantity() - 1);

                if ($orderItem->getQuantity() == 0) {
                    $this->orderService->deleteOrderItem($orderItem->getOrderItemId());
                    $order->removeOrderItem($orderItem);
                } else {
                    $this->orderService->updateOrderItem($orderItem->getOrderItemId(), $orderItem);
                }
            }
        }
        return $order;
    }

    public function deleteWholeItem($ticketLinkId): Order
    {
        $order = $this->getCart();

        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getTicketLinkId() == $ticketLinkId) {
                $this->orderService->deleteOrderItem($orderItem->getOrderItemId());
                $order->removeOrderItem($orderItem);
                return $order;
            }
        }
        throw new ObjectNotFoundException("Specified item not found.");
    }


    public function getCartAfterLogin($customer): void
    {
        $customerOrder = $this->orderService->getCartOrderForCustomer($customer->getUserId());

        if (!$customerOrder && $this->cartIsInitialised()) {
            $order = $this->getCart();
            $order->setCustomer($customer);
            $this->orderService->updateOrder($order->getOrderId(), $order);
            return;
        }

        if (!$customerOrder && !$this->cartIsInitialised()) {
            return;
        }

        if ($this->cartIsInitialised() && ($_SESSION["cartId"] != $customerOrder->getOrderId())) {

            $sessionOrder = $this->orderService->getOrderById($_SESSION["cartId"]);
            $mergedOrder = $this->orderService->mergeOrders($customerOrder, $sessionOrder);
            $_SESSION["cartId"] = $mergedOrder->getOrderId();
        }

        $_SESSION["cartId"] = $customerOrder->getOrderId();
    }


    private function checkValidCheckout(): Order
    {
        if (!isset($_SESSION["user"]))
        throw new AuthenticationException("User not logged in.");

         $user = unserialize($_SESSION["user"]);

        if (!$user instanceof Customer)
            throw new AuthenticationException("Only customers are allowed to check out.");

        if ($user->getUserId() != $this->getCart()->getCustomer()->getUserId())
            throw new AuthenticationException("Only the owner of the cart is authorised to checkout.");

        if (!$this->cartIsInitialised())
            throw new CartException("Cart not initialised.");

        $cartOrder = $this->getCart();

        if ($cartOrder->getOrderItems() == null)
            throw new CartException("Cart is empty.");

        return $cartOrder;
    }
        // In CartService.php
    public function checkoutCart(): Order
    {
        $cartOrder = $this->getCart();
        // 1. Validate cart and permissions
        $cartOrder = $this->checkValidCheckout();
        
        try {
            // 2. Finalize current order
            $cartOrder->setIsPaid(true);
            $finalizedOrder = $this->orderService->finalizeOrder($cartOrder->getOrderId());
            
            // 3. Clear current cart from session
            $this->clearCartSession();
            
            // 4. Create new empty cart for future items
            $this->initializeNewCart();
            
            return $finalizedOrder;
            
        } catch (Exception $e) {
            throw new CartException("Checkout failed: " . $e->getMessage());
        }
    }

    private function clearCartSession(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION["cartId"]);
    }

    private function initializeNewCart(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Create new empty order
        $newOrder = $this->orderService->createEmptyOrder();
        
        if (isset($_SESSION["user"])) {
            $user = unserialize($_SESSION["user"]);
            if ($user instanceof Customer) {
                $newOrder->setCustomer($user);
                $this->orderService->updateOrder($newOrder->getOrderId(), $newOrder);
            }
        }
        
        $_SESSION["cartId"] = $newOrder->getOrderId();
    }
    
}
