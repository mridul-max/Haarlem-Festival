<?php
require_once(__DIR__ . "/../APIController.php");
require_once(__DIR__ . "/../../../services/CartService.php");
require_once(__DIR__ . "/../../../models/TicketLink.php");
require_once(__DIR__ . "/../../../services/TicketLinkService.php");
require_once(__DIR__ . "/../../../models/Exceptions/MissingVariableException.php");

class CartAPIController extends APIController
{
    private $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    protected function handleGetRequest($uri)
    {
        try {
            if ($uri == "/api/cart/count") {
                //api/cart/count GET - returns the amount of items in the cart
                $count = $this->cartService->getCount();
                $response = ["count" => $count];
                parent::sendResponse($response);
            } else if ($uri == "/api/cart") {
                //api/cart GET - returns the cart order as an order object
                $cartOrder = $this->cartService->getCart();
                parent::sendResponse($cartOrder);
            } else if ($uri == "/api/cart/checkpayment") {
                $cartOrder = $this->cartService->checkIfPaid();
                echo json_encode($cartOrder);
            } else
                throw new Exception("Bad request.", 400);
        } catch (Throwable $e) {
            Logger::write($e);
            parent::sendErrorMessage($e->getMessage(), $e->getCode());
        }
    }

    protected function handlePostRequest($uri)
    {
        try {
            //api/cart/add/{ticketlinkId} POST method - adds one item of the ticket link to the cart order
            if (str_starts_with($uri, "/api/cart/add/") && is_numeric(basename($uri))) {
                $ticketLinkId = basename($uri);
                $cartOrder = $this->cartService->addItem($ticketLinkId);
                parent::sendResponse($cartOrder);
                return;
            }
            //api/cart/remove/{ticketlinkId} POST method - removes one item of the ticket link from the cart order
            else if (str_starts_with($uri, "/api/cart/remove/") && is_numeric(basename($uri))) {
                $ticketLinkId = basename($uri);
                $cartOrder = $this->cartService->removeItem($ticketLinkId);
                parent::sendResponse($cartOrder);
                return;
            }
<<<<<<< Updated upstream
            ///api/cart/checkout POST method - checks out the cart
            else if (str_starts_with($uri, "/api/cart/checkout")) {
                $paymentMethod = $this->getPaymentMethodFromPost();
                $paymentUrl = $this->cartService->checkoutCart($paymentMethod);
                echo json_encode(["paymentUrl" => $paymentUrl]);
=======
            else if (str_starts_with($uri, "/api/cart/checkout/")) {
                $chkout = $this->cartService->checkoutCart();
                parent::sendResponse($chkout);
>>>>>>> Stashed changes
                return;
            } else {
                throw new Exception("Bad request.", 400);
            }
        } catch (Throwable $e) {
            Logger::write($e);
            parent::sendErrorMessage($e->getMessage(), $e->getCode());
        }
    }

    protected function handlePutRequest($uri)
    {
        parent::sendErrorMessage("Method not allowed.", 405);
    }

    protected function handleDeleteRequest($uri)
    {
        try {
            if (str_starts_with($uri, "/api/cart/item/") && is_numeric(basename($uri))) {
                $ticketLinkId = basename($uri);
                $cartOrder = $this->cartService->deleteWholeItem($ticketLinkId);
                parent::sendResponse($cartOrder);
                return;
            } else {
                throw new Exception("Bad Request", 400);
            }
        } catch (Throwable $e) {
            parent::sendErrorMessage($e->getMessage(), $e->getCode());
        }
    }

    private function getPaymentMethodFromPost()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if (!isset($data->paymentMethod))
            throw new MissingVariableException("Payment method not specified.", 400);
        return $data->paymentMethod;
    }
}
