<?php
require_once(__DIR__ . "/../services/ApiKeyService.php");
require_once(__DIR__ . "/../models/ApiKey.php");
require_once(__DIR__ . '/../models/User.php');

class ApiKeyController
{
    private $service;
    const MANAGE_VIEW = __DIR__ . "/../views/admin/manageApiKeys.php";

    public function __construct()
    {
        $this->service = new ApiKeyService();
    }

    public function init()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'POST') {
                $this->createKey();
            } elseif ($_POST['action'] === 'DELETE') {
                $this->deleteKey();
            } else {
                echo "Invalid action";
            }
        } else {
            echo $_SERVER['REQUEST_METHOD'];
            $this->viewManagementPage();
        }
    }

    public function viewManagementPage()
    {
        $apiKeys = $this->service->getAll();
        require_once(self::MANAGE_VIEW);
    }

    public function createKey()
    {
        $name = $_POST['name'];
        $this->service->createKey($name);

        header("Location: /manageApiKeys");
    }

    public function deleteKey()
    {
        $this->service->revoke($_POST['id']);

        header("Location: /manageApiKeys");
    }
}
