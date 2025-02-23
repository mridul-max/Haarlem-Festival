<?php

class Cart {
    private int $cartId;
    private int $customerId;
    private DateTime $createdDate;
    private array $cartItems = [];

    public function __construct(
        int $customerId,
        DateTime $createdDate = new DateTime(),
        array $cartItems = []
    ) {
        $this->customerId = $customerId;
        $this->createdDate = $createdDate;
        $this->cartItems = $cartItems;
    }

    // Getters
    public function getCartId(): int {
        return $this->cartId;
    }

    public function getCustomerId(): int {
        return $this->customerId;
    }

    public function getCreatedDate(): DateTime {
        return $this->createdDate;
    }

    public function getCartItems(): array {
        return $this->cartItems;
    }

    // Setters
    public function setCartId(int $cartId): void {
        $this->cartId = $cartId;
    }

    public function setCustomerId(int $customerId): void {
        $this->customerId = $customerId;
    }

    // Cart item management
    public function addItem(CartItem $item): void {
        $this->cartItems[] = $item;
    }

    public function removeItem(int $cartItemId): void {
        $this->cartItems = array_filter(
            $this->cartItems,
            fn($item) => $item->getCartItemId() !== $cartItemId
        );
    }

    public function clearCart(): void {
        $this->cartItems = [];
    }

    public function getTotalItems(): int {
        return count($this->cartItems);
    }

    public function getTotalQuantity(): int {
        return array_reduce(
            $this->cartItems,
            fn($total, $item) => $total + $item->getQuantity(),
            0
        );
    }
}

