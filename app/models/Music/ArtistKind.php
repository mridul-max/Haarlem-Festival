<?php

class ArtistKind implements JsonSerializable
{
    private $id;
    private $name;

    public function __construct($id, $name)
    {
        $this->setId($id);
        $this->setName($name);
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

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName()
        ];
    }
}
