<?php
require_once(__DIR__ . "/../APIController.php");
require_once(__DIR__ . "/../../../services/OrderService.php");
require_once(__DIR__ . "/../../../models/Order.php");
require_once(__DIR__ . "/../../../services/ApiKeyService.php");

class OrderAPIController extends APIController
{
    private $orderService;
    private $apiKeyService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->apiKeyService = new ApiKeyService();
    }

    public function handleGetRequest($uri)
    {
        try {
            if (is_numeric(basename($uri))) {
                $this->getOrderById(basename($uri));
                return;
            }
            $this->getAllOrders();
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage($e->getMessage(), 500);
        }
    }

    private function getAllOrders()
    {
        $customerId = null;

        if (isset($_GET['customerId'])) {
            $customerId = $_GET['customerId'];
        }

        $orders = $this->orderService->getOrdersToExport($customerId);
        echo json_encode($orders);
    }

    private function getOrderById($id)
    {
        $orders = $this->orderService->getOrderById($id);
        echo json_encode($orders);
    }
}
