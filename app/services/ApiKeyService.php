<?php
require_once(__DIR__ . "/../repositories/ApiKeyRepository.php");
require_once(__DIR__ . "/../models/ApiKey.php");

class ApiKeyService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new ApiKeyRepository();
    }

    /**
     * Gets all ApiKeys.
     */
    public function getAll(): array
    {
        return $this->repo->getAll();
    }

    /**
     * Revokes an ApiKey.
     * @param $id int The id of the ApiKey to revoke.
     */
    public function revoke($id)
    {
        $this->repo->delete($id);
    }

    /**
     * Checks if a key is valid.
     * @param $key string The key to check.
     */
    public function isKeyValid($token): bool
    {
        $token = htmlspecialchars($token);
        return $this->repo->isKeyValid($token);
    }

    public function createKey($name): ApiKey
    {
        $name = htmlspecialchars($name);
        $key = $this->generateRandomString(32);
        return $this->repo->insert($key, $name);
    }

    // Borrowed from: https://stackoverflow.com/questions/4356289/php-random-string-generator
    private function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
