<?php
require_once(__DIR__ . "/../APIController.php");
require_once(__DIR__ . "/../../../services/CartService.php");
require_once(__DIR__ . "/../../../models/TicketLink.php");
require_once(__DIR__ . "/../../../services/TicketLinkService.php");
require_once(__DIR__ . "/../../../models/Exceptions/MissingVariableException.php");

class CartAPIController extends APIController {
    private $cartService;

    public function __construct() {
        $this->cartService = new CartService();
    }

    protected function handleGetRequest($uri) {
        try {
            if ($uri == "/api/cart/count") {
                $count = $this->cartService->getCount();
                parent::sendResponse(["count" => $count]);
            } else if ($uri == "/api/cart") {
                parent::sendResponse($this->cartService->getCart());
            } else {
                throw new Exception("Bad request.", 400);
            }
        } catch (AuthenticationException $e) {
            parent::sendErrorMessage("Authentication required", 401);
        } catch (Exception $e) {
            parent::sendErrorMessage($e->getMessage(), $e->getCode());
        }
    }

    protected function handlePostRequest($uri) {
        try {
            if (str_starts_with($uri, "/api/cart/add/") && is_numeric(basename($uri))) {
                $this->cartService->addItem(basename($uri));
                parent::sendResponse($this->cartService->getCart());
            } else if (str_starts_with($uri, "/api/cart/remove/") && is_numeric(basename($uri))) {
                $this->cartService->removeItem(basename($uri));
                parent::sendResponse($this->cartService->getCart());
            } else if ($uri == "/api/cart/checkout") {
                $order = $this->cartService->checkoutCart();
                parent::sendResponse([
                    'message' => 'Checkout successful',
                    'orderId' => $order->getOrderId()
                ]);
            } else {
                throw new Exception("Bad request.", 400);
            }
        } catch (AuthenticationException $e) {
            parent::sendErrorMessage("Authentication required", 401);
        } catch (InsufficientTicketsException $e) {
            parent::sendErrorMessage($e->getMessage(), 409);
        } catch (Exception $e) {
            parent::sendErrorMessage($e->getMessage(), $e->getCode());
        }
    }
    // Keep other methods the same
    protected function handlePutRequest($uri)
    {
        parent::sendErrorMessage("Method not allowed.", 405);
    }

    protected function handleDeleteRequest($uri)
    {
        try {
            if (str_starts_with($uri, "/api/cart/item/") && is_numeric(basename($uri))) {
                $ticketLinkId = basename($uri);
                $this->cartService->deleteWholeItem($ticketLinkId);
                parent::sendResponse($this->cartService->getCart());
                return;
            } else {
                throw new Exception("Bad Request", 400);
            }
        } catch (Throwable $e) {
            parent::sendErrorMessage($e->getMessage(), $e->getCode());
        }
    }

}
