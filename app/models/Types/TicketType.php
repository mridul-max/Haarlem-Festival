<?php
class TicketType implements JsonSerializable
{
    private $id;
    private $name;
    private $price;
    private $nrOfPeople;

    public function __construct($id, $name, $price, $nrOfPeople)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->nrOfPeople = $nrOfPeople;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function setPrice($value)
    {
        $this->price = $value;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getNrOfPeople()
    {
        return $this->nrOfPeople;
    }

    public function setNrOfPeople($value)
    {
        $this->nrOfPeople = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "price" => $this->getPrice(),
            "nrOfPeople" => $this->getNrOfPeople()
        ];
    }
}
