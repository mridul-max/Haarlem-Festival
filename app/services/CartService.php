<?php
require_once(__DIR__ . '/../repositories/CartRepository.php');
require_once(__DIR__ . '/../models/Exceptions/EventSoldOutException.php');
require_once(__DIR__ . '/../models/Exceptions/CartException.php');
require_once(__DIR__ . '/../models/Exceptions/AuthenticationException.php');
require_once(__DIR__ . '/OrderService.php');
require_once(__DIR__ . '/CustomerService.php');

class CartService {
    private $cartRepository;
    private $orderService;
    private $customerService;

    public function __construct() {
        $this->cartRepository = new CartRepository();
        $this->orderService = new OrderService();
        $this->customerService = new CustomerService();
    }

    private function getAuthenticatedCustomer(): Customer {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION["user"])) {
            throw new AuthenticationException("User not logged in.");
        }
        
        $user = unserialize($_SESSION["user"]);
        if (!$user instanceof Customer) {
            throw new AuthenticationException("Invalid user type.");
        }
        return $user;
    }

    public function getCart(): array {
        try {
            $customer = $this->getAuthenticatedCustomer();
            $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId());
            
            if (!$cart) {
                return [];
            }
            
            return array_map(function($item) {
                return [
                    'ticketLinkId' => $item->getTicketLinkId(),
                    'quantity' => $item->getQuantity()
                ];
            }, $cart->getCartItems());
        } catch (AuthenticationException $e) {
            return [];
        }
    }

    public function getCount(): int {
        try {
            $customer = $this->getAuthenticatedCustomer();
            $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId());
            return $cart ? $cart->getTotalQuantity() : 0;
        } catch (AuthenticationException $e) {
            return 0;
        } catch (Exception $e) {
            // Log the error if you have logging configured
            // error_log($e->getMessage());
            return 0;  // Return 0 for any unexpected errors
        }
    }

    public function addItem($ticketLinkId): void {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId()) ?? new Cart($customer->getUserId());
        
        // Add or update item
        $existingItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getTicketLinkId() == $ticketLinkId) {
                $existingItem = $item;
                break;
            }
        }
        
        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $cart->addItem(new CartItem($ticketLinkId, 1, $cart->getCartId()));
        }
        
        $this->cartRepository->saveCart($cart);
    }

    public function removeItem($ticketLinkId): void {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId());
        
        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                if ($item->getTicketLinkId() == $ticketLinkId) {
                    $newQty = $item->getQuantity() - 1;
                    if ($newQty > 0) {
                        $item->setQuantity($newQty);
                    } else {
                        $cart->removeItem($item->getCartItemId());
                    }
                    break;
                }
            }
            $this->cartRepository->saveCart($cart);
        }
    }

    public function deleteWholeItem($ticketLinkId): void {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId());
        
        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                if ($item->getTicketLinkId() == $ticketLinkId) {
                    $cart->removeItem($item->getCartItemId());
                    break;
                }
            }
            $this->cartRepository->saveCart($cart);
        }
    }

    public function checkoutCart(): Order {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId());
        
        if (!$cart || count($cart->getCartItems()) === 0) {
            throw new CartException("Cart is empty.");
        }
        
        try {
            $this->cartRepository->beginTransaction();
            
            // Create order through OrderService
            $order = $this->orderService->createOrder($customer->getUserId());
            
            // Add items through OrderService
            foreach ($cart->getCartItems() as $cartItem) {
                $this->orderService->createOrderItem(
                    $cartItem->getTicketLinkId(),
                    $order->getOrderId(),
                    $cartItem->getQuantity()
                );
            }
            
            // Clear cart after successful checkout
            $this->cartRepository->deleteCart($cart->getCartId());
            
            $this->cartRepository->commit();
            return $order;
        } catch (Exception $e) {
            $this->cartRepository->rollBack();
            throw $e;
        }
    }

    public function clearCart(): void {
        $customer = $this->getAuthenticatedCustomer();
        $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId());
        if ($cart) {
            $this->cartRepository->deleteCart($cart->getCartId());
        }
    }
}