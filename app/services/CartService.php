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

        //Check if the cart is initialised.
        return (isset($_SESSION["cartId"]));
    }

    /**
     * Is called to create a new cart order if there is none in session when it's required to be.
     * @param $ticketLinkId
     * @return Order
     * @throws CartException
     */
    private function initialiseCart($ticketLinkId): Order
    {
        if ($this->cartIsInitialised()) {
            throw new CartException("Cart is already initialised.");
        }

        //If a customer is logged in then we use their id, else we don't pass a customer id.
        if (isset($_SESSION["user"])) {
            $user = unserialize($_SESSION["user"]);

            //Only visitors or customers are able to buy tickets, not admins or staff.
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

    /**
     * Gets the cart from the session.
     * @return Order
     * @throws CartException
     */
    public function getCart(): Order
    {
        if (!$this->cartIsInitialised()) {
            throw new CartException("Cart is not initialised.");
        }

        //Retrieve the order that is in cart from the db and return it.
        $orderId = $_SESSION["cartId"];
        return $this->orderService->getOrderById($orderId);
    }

    /**
     * Gets the total item count from the session.
     * @return int
     */
    public function getCount(): int
    {
        if (!$this->cartIsInitialised()) {
            return 0;
        } else {
            $orderId = $_SESSION["cartId"];
            $order = $this->orderService->getOrderById($orderId);
            //Returns the total number of items in the order
            return $order->getTotalItemCount();
        }
    }

    /**
     * Used when sharing a cart with another user.
     * @param $orderId
     * @return Order
     */
    public function getCartByOrderId($orderId): Order
    {
        return $this->orderService->getOrderById($orderId);
    }

    /**
     * Adds one item to the cart.
     * @param $ticketLinkId
     * The ID of the ticketlink to add.
     * @return Order
     * @throws CartException
     */
    public function addItem($ticketLinkId): Order
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        //If cart wasn't initialised, we initialise it using the ticket.
        if (!isset($_SESSION["cartId"])) {
            return $this->initialiseCart($ticketLinkId);
        } else {
            //Retrieve the order that is in cart from the db.
            $order = $this->orderService->getOrderById($_SESSION["cartId"]);

            //Check if an orderitem with the same ticketlinkid already exists in the order.
            foreach ($order->getOrderItems() as $orderItem) {
                if ($orderItem->getTicketLinkId() == $ticketLinkId) {
                    //If so, add to the quantity of the orderItem and update the orderItem.
                    $orderItem->setQuantity($orderItem->getQuantity() + 1);
                    $this->orderService->updateOrderItem($orderItem->getOrderItemId(), $orderItem);
                    return $order;
                }
            }
            //If not, then we create a new orderitem with the ticketlinkid and add it to the order.
            $orderItem = $this->orderService->createOrderItem($ticketLinkId, $order->getOrderId());
            $order->addOrderItem($orderItem);
        }
        return $order;
    }

    /**
     * Removes one item from the cart.
     * @param $ticketLinkId
     * The ID of the item to remove.
     * @return Order returns the order object.
     * @throws CartException
     */
    public function removeItem($ticketLinkId): Order
    {
        //Retrieve the order that is in cart from the db.
        $order = $this->getCart();

        //Check for the orderitem that contains the ticketlinkId
        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getTicketLinkId() == $ticketLinkId) {
                //If so, subtract 1 from the quantity of the orderItem.
                $orderItem->setQuantity($orderItem->getQuantity() - 1);

                if ($orderItem->getQuantity() == 0) {
                    //If the quantity is 0, then we remove the orderitem from the order.
                    $this->orderService->deleteOrderItem($orderItem->getOrderItemId());
                    $order->removeOrderItem($orderItem);
                } else {
                    //If not, then we only update the orderitem with the new quantity.
                    $this->orderService->updateOrderItem($orderItem->getOrderItemId(), $orderItem);
                }
            }
        }
        return $order;
    }

    /**
     * Removes the whole order item from the cart.
     * @param $ticketLinkId
     * The ID of the item to remove.
     * @return Order returns the order object.
     * @throws ObjectNotFoundException | CartException
     */
    public function deleteWholeItem($ticketLinkId): Order
    {
        //Get order from the cart
        $order = $this->getCart();

        //Find the order item that contains the ticket link
        foreach ($order->getOrderItems() as $orderItem) {
            //Delete the order item from the database and from the order, return order
            if ($orderItem->getTicketLinkId() == $ticketLinkId) {
                $this->orderService->deleteOrderItem($orderItem->getOrderItemId());
                $order->removeOrderItem($orderItem);
                return $order;
            }
        }
        //If the order item was not found, throw an exception
        throw new ObjectNotFoundException("Specified item not found.");
    }

    /**
     * @param $customerId
     * @return void
     * @throws CartException
     */
    public function getCartAfterLogin($customer): void
    {
<<<<<<< Updated upstream
        //Fetch
        $customerOrder = $this->orderService->getCartOrderForCustomer($customer->getUserId());
=======
        
        //$customerOrder = $this->orderService->getCartOrderForCustomer($customer->getUser Id());
>>>>>>> Stashed changes

        //If there is no cart order saved for the customer,
        // but there is a cart in session,
        // we link the cart to the customer.
        if (!$customerOrder && $this->cartIsInitialised()) {
            $order = $this->getCart();
            $order->setCustomer($customer);
            $this->orderService->updateOrder($order->getOrderId(), $order);
            return;
        }

        //If there is no cart order saved for the customer and in the session, return
        if (!$customerOrder && !$this->cartIsInitialised()) {
            return;
        }

        //If there is already a cart in session and the logged-in user has another cart in db, we merge the carts
        if ($this->cartIsInitialised() && ($_SESSION["cartId"] != $customerOrder->getOrderId())) {
            $sessionOrder = $this->orderService->getOrderById($_SESSION["cartId"]);
            $mergedOrder = $this->orderService->mergeOrders($customerOrder, $sessionOrder);

            //Overwrite the session cart with the merged order.
            $_SESSION["cartId"] = $mergedOrder->getOrderId();
        } else {
            $_SESSION["cartId"] = $customerOrder->getOrderId ();
        }
<<<<<<< Updated upstream

        //If not, then we store the saved customer cart into the session so they can pick up where they left off.
        $_SESSION["cartId"] = $customerOrder->getOrderId();
=======
>>>>>>> Stashed changes
    }


    /**
     * Fetches the cart order from db and the validates the checkout values.
     * @return Order
     * @throws AuthenticationException
     * @throws CartException
     */
    private function checkValidCheckout(): Order
    {
<<<<<<< Updated upstream
        //Cart must be initialised
        if (!$this->cartIsInitialised())
=======
        if (!$this->cartIsInitialised()) {
>>>>>>> Stashed changes
            throw new CartException("Cart not initialised.");
        }

        //Fetch order from db
        $cartOrder = $this->getCart();

<<<<<<< Updated upstream
        //Order must not be paid already
        if ($cartOrder->getIsPaid())
            throw new CartException("Cart already paid.");

        //Order must not be empty
        if ($cartOrder->getOrderItems() == null)
=======
        if ($cartOrder->getOrderItems() == null || count($cartOrder->getOrderItems()) == 0) {
>>>>>>> Stashed changes
            throw new CartException("Cart is empty.");
        }

<<<<<<< Updated upstream
        //A user must be logged in
        if (!isset($_SESSION["user"]))
=======
        if (!isset($_SESSION["user"])) {
>>>>>>> Stashed changes
            throw new AuthenticationException("User not logged in.");
        }

        //Fetch user from session
        $user = unserialize($_SESSION["user"]);

<<<<<<< Updated upstream
        //The user must be a customer
        if (!$user instanceof Customer)
=======
        if (!$user instanceof Customer) {
>>>>>>> Stashed changes
            throw new AuthenticationException("Only customers are allowed to check out.");
        }

<<<<<<< Updated upstream
        //The customer must be owner of the cart
        if ($user->getUserId() != $this->getCart()->getCustomer()->getUserId())
=======
        if ($user->getUserId() != $this->getCart()->getCustomer()->getUserId()) {
>>>>>>> Stashed changes
            throw new AuthenticationException("Only the owner of the cart is authorised to checkout.");
        }

        return $cartOrder;
    }
    public function checkoutCart(): Order
    {
        $cartOrder = $this->checkValidCheckout();
        
        // Create new paid order
        $newOrder = new Order();
        $newOrder->setOrderDate(new DateTime());
        $newOrder->setIsPaid(true);
        $newOrder->setCustomer($cartOrder->getCustomer());

        // Clone cart items to new order
        foreach ($cartOrder->getOrderItems() as $item) {
            $orderItem = new OrderItem();
            $orderItem->setTicketLinkId($item->getTicketLinkId());
            $orderItem->setQuantity($item->getQuantity());
            $newOrder->addOrderItem($orderItem);
        }

        // Persist the new order
        $savedOrder = $this->orderService->createPersistedOrder($newOrder);
        
        // Clear cart
        $this->orderService->deleteOrder($cartOrder->getOrderId());
        $this->clearCart();

        return $savedOrder;
    }
    
    public function clearCart(): void
    {
        // Remove the cart ID from session
        unset($_SESSION["cartId"]);
    
        // Optionally destroy the entire session if needed
        // session_destroy(); // Uncomment if you want to clear the entire session
    }
}