<?php

class CartItem {
    private int $cartItemId;
    private int $ticketLinkId;
    private int $quantity;
    private int $cartId;

    public function __construct(
        int $cartItemId,
        int $ticketLinkId,
        int $quantity,
        int $cartId
    ) {
        $this->cartItemId = $cartItemId;
        $this->ticketLinkId = $ticketLinkId;
        $this->quantity = $quantity;
        $this->cartId = $cartId;
    }
    public function jsonSerialize(): mixed {
        return [
            'cartItemId' => $this->cartItemId,
            'ticketLinkId' => $this->ticketLinkId,
            'quantity' => $this->quantity,
            'cartId' => $this->cartId
        ];
    }

    // Getters
    public function getCartItemId(): int {
        return $this->cartItemId;
    }

    public function getTicketLinkId(): int {
        return $this->ticketLinkId;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getCartId(): int {
        return $this->cartId;
    }

    // Setters
    public function setCartItemId(int $cartItemId): void {
        $this->cartItemId = $cartItemId;
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }

    public function updateQuantity(int $delta): void {
        $this->quantity += $delta;
        if ($this->quantity < 0) {
            $this->quantity = 0;
        }
    }
}