<?php

class Address implements JsonSerializable
{
    private int $addressId;
    private string $streetName;
    private string $houseNumber;
    private string $postalCode;
    private string $city;
    private string $country;

    public function jsonSerialize(): mixed
    {
        return [
            'addressId' => $this->addressId,
            'streetName' => $this->streetName,
            'houseNumber' => $this->houseNumber,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'country' => $this->country
        ];
    }

    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    public function getAddressId()
    {
        return $this->addressId;
    }

    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    }

    public function getStreetName()
    {
        return $this->streetName;
    }

    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;
    }

    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getAddressLine1(){
        return ($this->streetName . " " . $this->houseNumber);
    }

    public function getAddressLine2(){
        return ($this->postalCode . " " . $this->city);
    }
}
