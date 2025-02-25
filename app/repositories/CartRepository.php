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

    
    
    private function findCartData(int $customerId): ?array {
        $stmt = $this->connection->prepare(
            "SELECT cartId, customerId, createdDate 
             FROM carts 
             WHERE customerId = :customerId"
        );
        $stmt->execute([':customerId' => $customerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    private function refreshCartItems(Cart $cart): void {
        $cart->setCartItems($this->getCartItems($cart->getCartId()));
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
    public function findCartByCustomerId(int $customerId): ?Cart {
        // Get cart with explicit columns
        $sql = "SELECT cartId, customerId, createdDate FROM carts WHERE customerId = :customerId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":customerId", $customerId, PDO::PARAM_INT);
        $stmt->execute();

        $cartData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cartData) {
            return null;
        }

        $cart = $this->buildCart($cartData);
        $cart->items = $this->getCartItems($cart->cartId);
        return $cart;
    }
    public function updateItemQuantity(int $cartItemId, int $newQuantity): void {
        $sql = "UPDATE cartitems SET quantity = :quantity WHERE cartItemId = :cartItemId";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':quantity' => $newQuantity,
            ':cartItemId' => $cartItemId
        ]);
    }

    public function getTotalQuantity(int $cartId): int {
        $sql = "SELECT SUM(quantity) AS total FROM cartitems WHERE cartId = :cartId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":cartId", $cartId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    public function addCartItem(int $cartId, int $ticketLinkId, int $quantity): void {
        $sql = "INSERT INTO cartitems (cartId, ticketLinkId, quantity)
                VALUES (:cartId, :ticketLinkId, :quantity)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':cartId' => $cartId,
            ':ticketLinkId' => $ticketLinkId,
            ':quantity' => $quantity
        ]);
    }

    public function findCartItem(int $cartId, int $ticketLinkId): ?CartItem {
        $sql = "SELECT cartItemId, ticketLinkId, quantity 
                FROM cartitems 
                WHERE cartId = :cartId AND ticketLinkId = :ticketLinkId";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':cartId' => $cartId,
            ':ticketLinkId' => $ticketLinkId
        ]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->buildCartItem($data) : null;
    }

    private function getCartItems(int $cartId): array {
        // Explicit column selection
        $sql = "SELECT cartItemId, ticketLinkId, quantity, cartId 
                FROM cartitems 
                WHERE cartId = :cartId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":cartId", $cartId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([$this, 'buildCartItem'], $stmt->fetchAll());
    }

    public function saveCart(Cart $cart): void {
    // Only needed if you want to keep the bulk save capability
    $this->connection->beginTransaction();
    
    try {
        $this->connection->prepare("DELETE FROM cartitems WHERE cartId = :cartId")
            ->execute([':cartId' => $cart->getCartId()]);

        $stmt = $this->connection->prepare(
            "INSERT INTO cartitems (cartId, ticketLinkId, quantity) 
             VALUES (:cartId, :ticketLinkId, :quantity)"
        );

        foreach ($cart->getCartItems() as $item) {
            $stmt->execute([
                ':cartId' => $cart->getCartId(),
                ':ticketLinkId' => $item->getTicketLinkId(),
                ':quantity' => $item->getQuantity()
            ]);
        }
        
        $this->connection->commit();
    } catch (Exception $e) {
        $this->connection->rollBack();
        throw $e;
        }
    }

    public function buildCart(array $data): Cart 
    {
        //$cartId = (int)$data['cartId'];
        return new Cart(
            (int)$data['cartId'],
            (int)$data['customerId'],
            new DateTime($data['createdDate']),
            []// Load items
        );
    }
    
    // Fix buildCartItem() to include cartId
    public function buildCartItem(array $data): CartItem {
        return new CartItem(
            (int)$data['cartItemId'],
            (int)$data['ticketLinkId'],
            (int)$data['quantity'],
            (int)$data['cartId']
        );
    }
}