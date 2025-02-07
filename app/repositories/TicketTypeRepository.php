<?php

require_once(__DIR__ . "/../models/Types/TicketType.php");
require_once("Repository.php");

class TicketTypeRepository extends Repository
{
    private function buildTicketTypes($arr): array
    {
        $output = array();
        foreach ($arr as $ticketType) {
            $id = $ticketType['ticketTypeId'];
            $name = $ticketType['ticketTypeName'];
            $price = $ticketType['ticketTypePrice'];
            $quantity = $ticketType['nrOfPeople'];
            $ticketTypeEntry = new TicketType($id, $name, $price, $quantity);
            array_push($output, $ticketTypeEntry);
        }

        return $output;
    }

    public function getAll()
    {
        $sql = "SELECT ticketTypeId, ticketTypeName, ticketTypePrice, nrOfPeople FROM tickettypes";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $this->buildTicketTypes($result);
    }

    public function getById($id)
    {
        $sql = "SELECT ticketTypeId, ticketTypeName, ticketTypePrice, nrOfPeople FROM tickettypes WHERE ticketTypeId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $this->buildTicketTypes([$result])[0];
    }

    public function createTicketType($name, $price, $quantity): int
    {
        $sql = "INSERT INTO tickettypes (ticketTypeName, ticketTypePrice, nrOfPeople) VALUES (:name, :price, :quantity)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();

        return $this->connection->lastInsertId();
    }

    public function updateTicketType($id, $name, $price, $quantity)
    {
        $sql = "UPDATE tickettypes SET ticketTypeName = :name, ticketTypePrice = :price, nrOfPeople = :quantity WHERE ticketTypeId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM tickettypes WHERE ticketTypeId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
