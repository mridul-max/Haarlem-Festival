<?php
require_once(__DIR__ . "/../APIController.php");
require_once(__DIR__ . "/../../../services/OrderService.php");
require_once(__DIR__ . "/../../../models/Order.php");
require_once(__DIR__ . "/../../../services/ApiKeyService.php");
/**
 * This class is the controller for the Order API.
 * @author Joshua
 */
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
            // Getting access to this API requires either a valid API key, or a valid session.
            // Not logged in as admin? Try to get the API key.
            if (!$this->isLoggedInAsAdmin() && !$this->isApiKeyValid()) {
                $this->sendErrorMessage("Your API key is invalid, or you are not logged in as admin.", 401);
                return;
            }

            //api/orders/{id}
            if (is_numeric(basename($uri))) {
                $this->getOrderById(basename($uri));
                return;
            }

            //api/orders
            $this->getAllOrders();
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage($e->getMessage(), 500);
        }
    }

    private function getAllOrders()
    {
        $isPaid = null;
        $customerId = null;

        if (isset($_GET['isPaid'])) {
            $isPaid = $_GET['isPaid'];
        }

        if (isset($_GET['customerId'])) {
            $customerId = $_GET['customerId'];
        }

        $orders = $this->orderService->getOrdersToExport($isPaid, $customerId);
        echo json_encode($orders);
    }

    private function getOrderById($id)
    {
        $orders = $this->orderService->getOrderById($id);
        echo json_encode($orders);
    }
}
