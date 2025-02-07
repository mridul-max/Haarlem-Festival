<?php

require_once("Address.php");
class Location implements JsonSerializable
{
    private int $locationId;
    private string $name;
    private int $addressId;
    private Address $address;
    private int $locationType;
    private ?int $capacity;
    private $lon;
    private $lat;

    private ?string $description;


    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;
    }

    public function getLocationId()
    {
        return $this->locationId;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    public function getAddressId()
    {
        return $this->addressId;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;
    }

    public function getLocationType()
    {
        return $this->locationType;
    }

    public function getLocationTypeAsString()
    {
        return self::$LOCATION_TYPE_NAMES[$this->getLocationType()];
    }

    public static $LOCATION_TYPE_NAMES = [
        1 => "Jazz",
        2 => "Restaurant",
        3 => "History",
        4 => "DANCE!"
    ];

    public function setLon($lon)
    {
        $this->lon = $lon;
    }

    public function getLon()
    {
        return $this->lon;
    }

    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getLocationId(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'locationType' => $this->getLocationType(),
            'locationTypeFriendly' => $this->getLocationTypeAsString(),
            'capacity' => $this->getCapacity(),
            'lon' => $this->getLon(),
            'lat' => $this->getLat(),
            'description' => $this->getDescription()
        ];
    }
}
