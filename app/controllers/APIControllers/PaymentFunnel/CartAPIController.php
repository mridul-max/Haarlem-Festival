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
                $count = $this->cartService->getCount();
                $response = ["count" => $count];
                parent::sendResponse($response);
            } else if ($uri == "/api/cart") {
                $cartOrder = $this->cartService->getCart();
                parent::sendResponse($cartOrder);
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
            if (str_starts_with($uri, "/api/cart/add/") && is_numeric(basename($uri))) {
                $ticketLinkId = basename($uri);
                $cartOrder = $this->cartService->addItem($ticketLinkId);
                parent::sendResponse($cartOrder);
                return;
            }
            else if (str_starts_with($uri, "/api/cart/remove/") && is_numeric(basename($uri))) {
                $ticketLinkId = basename($uri);
                $cartOrder = $this->cartService->removeItem($ticketLinkId);
                parent::sendResponse($cartOrder);
                return;
            }
            else if (str_starts_with($uri, "/api/cart/checkout")) {
                $chkout = $this->cartService->checkoutCart();
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
}
