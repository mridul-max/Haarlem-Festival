<?php
require_once(__DIR__ . "/APIController.php");
require_once(__DIR__ . "/../../services/ImageService.php");

class ImageAPIController extends APIController
{
    private ImageService $service;

    public function __construct()
    {
        $this->service = new ImageService();
    }

    public function handleGetRequest($uri)
    {
        try {
            if (isset($_GET["search"])) {
                $images = $this->service->search($_GET["search"]);
                echo json_encode($images);
                return;
            }

            if (is_numeric(basename($uri))) {
                $image = $this->service->getImageById(basename($uri));
                echo json_encode($image);
                return;
            }

            $images = $this->service->getAll();
            echo json_encode($images);
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrieve images.", 500);
        }
    }

}