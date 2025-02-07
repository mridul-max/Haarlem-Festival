<?php

require_once("Restaurant.php");
require_once(__DIR__ . "/../Event.php");

class RestaurantEvent extends Event implements JsonSerializable
{
    private Restaurant $restaurant;

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    public function jsonSerialize(): mixed
    {
        return array_merge(parent::jsonSerialize(), [
            'restaurant' => $this->restaurant
        ]);
    }
}
