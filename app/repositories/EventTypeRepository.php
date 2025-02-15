<?php

require_once(__DIR__ . "/../models/Types/EventType.php");
require_once("Repository.php");

class EventTypeRepository extends Repository
{
    private function build($arr): array
    {
        $output = [];
        foreach ($arr as $item) {
            $id = $item['eventTypeId'];
            $name = $item['name'];
            $vat = $item['VAT'];

            $eventType = new EventType($id, $name, $vat);
            array_push($output, $eventType);
        }

        return $output;
    }

    public function getAll(): array
    {
        $sql = "SELECT eventTypeId, name, VAT FROM festivaleventtypes";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $this->build($result);
    }

    /**
     * @throws ObjectNotFoundException
     */
    public function getById($id): EventType
    {
        $sql = "SELECT eventTypeId, name, VAT FROM festivaleventtypes WHERE eventTypeId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $arr = $this->build($result);
        if (empty($arr)) {
            throw new ObjectNotFoundException("Event type with id: " . $id . " not found.", 404);
        }
        return $arr[0];
    }

    public function insert($name, $vat): int
    {
        $sql = "INSERT INTO festivaleventtypes (name, VAT) VALUES (:name, :vat)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':VAT', $vat, PDO::PARAM_STR);
        $stmt->execute();

        return $this->connection->lastInsertId();
    }

    public function update($id, $name, $vat)
    {
        $sql = "UPDATE festivaleventtypes SET name = :name, VAT = :vat WHERE eventTypeId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':vat', $vat, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM festivaleventtypes WHERE eventTypeId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
