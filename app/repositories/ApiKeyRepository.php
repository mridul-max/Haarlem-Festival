<?php

require_once(__DIR__ . '/../models/ApiKey.php');
require_once('Repository.php');

class ApikeyRepository extends Repository
{
    private function buildApiKeys($inputArr): array
    {
        $output = array();
        foreach ($inputArr as $apiKey) {
            $id = $apiKey['apiKeyId'];
            $token = $apiKey['token'];
            $name = $apiKey['name'];
            $apiKeyEntry = new ApiKey($id, $token, $name);
            array_push($output, $apiKeyEntry);
        }
        return $output;
    }

    public function getAll(): array
    {
        $sql = "SELECT apiKeyId, token, name FROM apikeys";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $this->buildApiKeys($result);
    }

    public function getById($id): ApiKey
    {
        $sql = "SELECT apiKeyId, token, name FROM apikeys WHERE apiKeyId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $this->buildApiKeys([$result])[0];
    }

    public function delete($id)
    {
        $sql = "DELETE FROM apikeys WHERE apiKeyId = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function isKeyValid($token): bool
    {
        $sql = "SELECT apiKeyId FROM apikeys WHERE token = :token";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return !is_bool($result);
    }

    public function insert($token, $name): ApiKey
    {
        $sql = "INSERT INTO apikeys (token, name) VALUES (:token, :name)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $id = $this->connection->lastInsertId();

        return $this->getById($id);
    }
}
