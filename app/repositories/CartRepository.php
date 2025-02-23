<?php
require_once(__DIR__ . "/Repository.php");
require_once(__DIR__ . "/../models/Cart.php");
require_once(__DIR__ . "/../models/CartItem.php");

class CartRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCartByCustomerId(int $customerId): Cart
    {
        $sql = "SELECT * FROM carts WHERE customerId = :customerId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":customerId", htmlspecialchars($customerId));
        $stmt->execute();

        $cartData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cartData) {
            // Create new cart if none exists
            return $this->createCart($customerId);
        }

        $cart = $this->buildCart($cartData);
        $cart->items = $this->getCartItems($cart->cartId);
        return $cart;
    }

    private function createCart(int $customerId): Cart
    {
        $sql = "INSERT INTO carts (customerId) VALUES (:customerId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":customerId", htmlspecialchars($customerId));
        $stmt->execute();

        return new Cart(
            $this->connection->lastInsertId(),
            $customerId,
            new DateTime(),
            []
        );
    }

    public function saveCart(Cart $cart): void
    {
        // Save cart items
        $this->connection->prepare("DELETE FROM cartitems WHERE cartId = :cartId")
            ->execute([':cartId' => $cart->cartId]);

        $sql = "INSERT INTO cartitems (cartId, ticketLinkId, quantity) VALUES (:cartId, :ticketLinkId, :quantity)";
        $stmt = $this->connection->prepare($sql);

        foreach ($cart->items as $item) {
            $stmt->bindValue(":cartId", htmlspecialchars($cart->cartId));
            $stmt->bindValue(":ticketLinkId", htmlspecialchars($item->ticketLinkId));
            $stmt->bindValue(":quantity", htmlspecialchars($item->quantity));
            $stmt->execute();
        }
    }

    private function getCartItems(int $cartId): array
    {
        $sql = "SELECT * FROM cartitems WHERE cartId = :cartId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":cartId", htmlspecialchars($cartId));
        $stmt->execute();

        return array_map([$this, 'buildCartItem'], $stmt->fetchAll());
    }

    private function buildCart(array $data): Cart
    {
        return new Cart(
            $data['cartId'],
            $data['customerId'],
            new DateTime($data['createdDate']),
            []
        );
    }

    private function buildCartItem(array $data): CartItem
    {
        return new CartItem(
            $data['cartItemId'],
            $data['ticketLinkId'],
            $data['quantity']
        );
    }
}