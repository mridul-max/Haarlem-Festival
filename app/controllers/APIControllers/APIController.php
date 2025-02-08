<?php

class APIController
{
    public function initialize($request)
    {
        error_reporting(0);

        if ($request == null) {
            $this->sendErrorMessage("Request cannot be empty.", 400);
            return;
        }

        try {
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                $this->handleGetRequest($request);
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->handlePostRequest($request);
            } elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
                $this->handlePutRequest($request);
            } elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {
                $this->handleDeleteRequest($request);
            }
        } catch (Exception $e) {
            $this->sendErrorMessage($e->getMessage());
        }
    }

    protected function handleGetRequest($uri)
    {
    }

    protected function handlePostRequest($uri)
    {
    }

    protected function handlePutRequest($uri)
    {
    }

    protected function handleDeleteRequest($uri)
    {
    }

    final protected function sendErrorMessage($message, $code = 500)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode(["error_message" => $message]);
    }

    final protected function sendSuccessMessage($message, $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(["success_message" => $message]);
    }

    final protected function sendResponse($body, $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($body);
    }


    final protected function isLoggedIn()
    {
        require_once(__DIR__ . '/../../models/User.php');
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION["user"])) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            Logger::write($e);
            return false;
        }
    }

    final protected function isLoggedInAsAdmin()
    {
        require_once(__DIR__ . '/../../models/User.php');
        try {
            if ($this->isLoggedIn()) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                return unserialize($_SESSION["user"])->getUserType() == "1";
            }

            return false;
        } catch (Exception $e) {
            Logger::write($e);
            return false;
        }
    }

    final protected function isApiKeyValid()
    {
        require_once(__DIR__ . "/../../services/ApiKeyService.php");
        $apiKey = $this->getApiKeyFromBearer();
        $apiKeyService = new ApiKeyService();
        return $apiKeyService->isKeyValid($apiKey);
    }

    final protected function getApiKeyFromBearer()
    {
        $bearer = "";
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $bearer = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } else {
            $bearer = $_SERVER['HTTP_AUTHORIZATION'];
        }
        $bearer = str_replace("Bearer ", "", $bearer);
        return $bearer;
    }
}
