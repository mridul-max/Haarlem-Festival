<?php

require_once(__DIR__ . "/APIController.php");
require_once(__DIR__ . "/../../services/PageService.php");


class TextPageAPIController extends APIController
{
    private $service;

    public function __construct()
    {
        $this->service = new PageService();
    }

    public function handleGetRequest($uri)
    {
        try {
            if (str_starts_with($uri, "/api/textpages")) {
                if (is_numeric(basename($uri))) {
                    $page = $this->service->getTextPageById(basename($uri));
                    if ($page == null) {
                        $this->sendErrorMessage("Page not found.", 404);
                        return;
                    }
                    echo json_encode($page);
                    return;
                }

                $pages = $this->service->getAllTextPages();
                echo json_encode($pages);
            } else {
                $this->sendErrorMessage("Invalid request.");
            }
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage($e->getMessage(), 500);
        }
    }

    public function handlePutRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        $data = json_decode(file_get_contents("php://input"));
        if ($data == null) {
            $this->sendErrorMessage("No data received.");
            return;
        }

        try {
            if (str_starts_with($uri, "/api/textpages") && is_numeric(basename($uri))) {
                if (!isset($data->title) || !isset($data->content) || !isset($data->images) || !isset($data->href)) {
                    $this->sendErrorMessage("Invalid data received. Required: title, content, images, href.", 400);
                    return;
                }

                $this->service->updateTextPage(basename($uri), $data->title, $data->content, $data->images, $data->href);

                // get page
                $page = $this->service->getTextPageById(basename($uri));
                echo json_encode($page);
                return;
            }

            $this->sendErrorMessage("Invalid request.");
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage($e->getMessage(), 400);
        }
    }

    public function handlePostRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        $data = json_decode(file_get_contents("php://input"));
        if ($data == null) {
            $this->sendErrorMessage("No data received.");
            return;
        }

        try {
            if (str_starts_with($uri, "/api/textpages")) {
                if (!isset($data->title) || !isset($data->content) || !isset($data->images) || !isset($data->href)) {
                    $this->sendErrorMessage("Invalid data received. Required: title, content, images, href.", 400);
                }

                $page = $this->service->createTextPage($data->title, $data->content, $data->images, $data->href);

                echo json_encode($page);
                return;
            }

            $this->sendErrorMessage("Invalid request.", 400);
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage($e->getMessage(), 400);
        }
    }

    public function handleDeleteRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        try {
            if (str_starts_with($uri, "/api/textpages") && is_numeric(basename($uri))) {
                $this->service->delete(basename($uri));
                $this->sendSuccessMessage("Page deleted successfully.");
                return;
            }

            $this->sendErrorMessage("Invalid request.");
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage($e->getMessage(), 400);
        }
    }
}
