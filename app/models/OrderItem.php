<?php
require_once("TicketLink.php");

class OrderItem implements JsonSerializable
{

    private int $orderItemId;
    private int $ticketLinkId;
    private string $eventName;
    private string $ticketName;
    private string $vatPercentage;
    private float $fullTicketPrice;
    private int $quantity;

    public function jsonSerialize(): mixed
    {
        return [
            "orderItemId" => $this->orderItemId,
            "ticketLinkId" => $this->ticketLinkId,
            "eventName" => $this->eventName,
            "ticketName" => $this->ticketName,
            "vatPercentage" => $this->vatPercentage,
            "fullTicketPrice" => $this->fullTicketPrice,
            "quantity" => $this->quantity,
            "totalBasePrice" => $this->getTotalBasePrice(),
            "totalVatAmount" => $this->getTotalVatAmount(),
            "totalFullPrice" => $this->getTotalFullPrice(),
        ];
    }

    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    public function setOrderItemId(int $orderItemId): void
    {
        $this->orderItemId = $orderItemId;
    }

    public function getTicketLinkId(): int
    {
        return $this->ticketLinkId;
    }

    public function setTicketLinkId(int $ticketLinkId): void
    {
        $this->ticketLinkId = $ticketLinkId;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): void
    {
        $this->eventName = $eventName;
    }

    public function getTicketName(): string
    {
        return $this->ticketName;
    }

    public function setTicketName(string $ticketName): void
    {
        $this->ticketName = $ticketName;
    }

    public function getVatPercentage(): string
    {
        return $this->vatPercentage;
    }

    public function setVatPercentage(string $vatPercentage): void
    {
        $this->vatPercentage = $vatPercentage;
    }

    public function getFullTicketPrice(): float
    {
        return $this->fullTicketPrice;
    }

    public function setFullTicketPrice(float $fullTicketPrice): void
    {
        $this->fullTicketPrice = $fullTicketPrice;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getBasePrice() : float
    {
        return round($this->fullTicketPrice / (1 + $this->vatPercentage), 2, PHP_ROUND_HALF_UP);
    }
    public function getVatAmount() : float
    {
        return round($this->vatPercentage * $this->getBasePrice(), 2, PHP_ROUND_HALF_UP);
    }

    public function getTotalBasePrice(): float
    {
        return round($this->getBasePrice() * $this->quantity, 2, PHP_ROUND_HALF_UP);
    }

    public function getTotalVatAmount(): float
    {
        return round($this->vatPercentage * $this->getTotalBasePrice(), 2, PHP_ROUND_HALF_UP);
    }

    public function getTotalFullPrice(): float
    {
        return $this->fullTicketPrice * $this->quantity;
    }
}
