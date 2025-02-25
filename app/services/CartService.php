<?php
require_once(__DIR__ . '/../repositories/CartRepository.php');
require_once(__DIR__ . '/../models/Exceptions/EventSoldOutException.php');
require_once(__DIR__ . '/../models/Exceptions/CartException.php');
require_once(__DIR__ . '/../models/Exceptions/AuthenticationException.php');
require_once(__DIR__ . '/../models/Cart.php');
require_once(__DIR__ . '/../models/CartItem.php');
require_once(__DIR__ . '/OrderService.php');
require_once(__DIR__ . '/CustomerService.php');

class CartService {
    private $cartRepository;
    private $orderService;
    private $customerService;
    //private $cart;

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
    public function getCartByCustomerId(int $customerId): Cart {
        $cartData = $this-> cartRepository->findCartData($customerId);
        
        if (!$cartData) {
            return $this->cartRepository->createCart($customerId);
        }
    
        $cart = $this->cartRepository->buildCart($cartData);
        $this->cartRepository->refreshCartItems($cart);
        return $cart;
    }

    public function getCount(): int {
        try {
            $customer = $this->getAuthenticatedCustomer();
            $cart = $this->cartRepository->findCartByCustomerId($customer->getUserId());
            
            if (!$cart) {
                return 0;
            }
            
            return $this->cartRepository->getTotalQuantity($cart->getCartId());
        } catch (AuthenticationException $e) {
            throw $e; // Re-throw to be caught by controller
        } catch (Exception $e) {
            throw $e; // Re-throw to be caught by controller
        }
    }
    public function getCartItems(): array {
        try {
            $customer = $this->getAuthenticatedCustomer();
            $cart = $this->getCartByCustomerId($customer->getUserId());
            return $cart->getCartItems();
        } catch (AuthenticationException $e) {
            throw new AuthenticationException("Authentication required to access cart");
        } catch (Exception $e) {
            error_log("Error retrieving cart items: " . $e->getMessage());
            throw new CartException("Could not retrieve cart items");
        }
    }

    public function getCart(): Cart {
        try {
            $customer = $this->getAuthenticatedCustomer();
            
            // Get or create cart using repository method that ensures cart existence
            $cart = $this->getCartByCustomerId($customer->getUserId());
            $cart->setCartItems($this->getCartItems());
            
            return $cart;
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }
            // Return proper JSON response
    public function addItem($ticketLinkId): void {
        try {
            $customer = $this->getAuthenticatedCustomer();
            $cart = $this->getCartByCustomerId($customer->getUserId());
    
            if (!$cart || !$cart->getCartId()) {
                throw new CartException("Cart initialization failed");
            }
    
            $existingItem = $this->cartRepository->findCartItem($cart->getCartId(), $ticketLinkId);
            
            if ($existingItem) {
                $this->cartRepository->updateItemQuantity(
                    $existingItem->getCartItemId(), 
                    $existingItem->getQuantity() + 1
                );
            } else {
                $this->cartRepository->addCartItem(
                    $cart->getCartId(),
                    $ticketLinkId,
                    1
                );
            }
        } catch (AuthenticationException $e) {
            throw $e; // Let controller handle
        } catch (Exception $e) {
            throw new Exception("Failed to update cart: " . $e->getMessage());
        }
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