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
 
}
