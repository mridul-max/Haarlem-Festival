<?php

require_once(__DIR__ . "/../repositories/EventTypeRepository.php");

class EventTypeService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new EventTypeRepository();
    }

    public function getAll(): array
    {
        return $this->repo->getAll();
    }

    public function getById(int $id): EventType
    {
        return $this->repo->getById($id);
    }


    public function add(EventType $obj): EventType
    {
        $name = htmlspecialchars($obj->getName());
        $vat = htmlspecialchars($obj->getVat());

        $id = $this->repo->insert($name, $vat);
        return $this->getById($id);
    }

    public function update(EventType $obj): EventType
    {
        $id = htmlspecialchars($obj->getId());
        $name = htmlspecialchars($obj->getName());
        $vat = htmlspecialchars($obj->getVat());

        $this->repo->update($id, $name, $vat);

        return $this->getById($id);
    }

    public function delete(EventType $obj): void
    {
        $id = htmlspecialchars($obj->getId());
        $this->repo->delete($id);
    }
}
