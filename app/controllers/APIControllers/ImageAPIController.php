<?php
require_once(__DIR__ . "/APIController.php");
require_once(__DIR__ . "/../../services/ImageService.php");

/**
 * @author Konrad
 */
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

    public function handleDeleteRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        try {
            if (str_starts_with($uri, "/api/images") && is_numeric(basename($uri))) {
                $this->service->removeImage(basename($uri));
                $this->sendSuccessMessage("Image removed.");
                return;
            }

            $this->sendErrorMessage("Invalid request.");
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to remove image.", 500);
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
            if (str_starts_with($uri, "/api/images") && is_numeric(basename($uri))) {
                if (!isset($data->alt)) {
                    throw new Exception("Invalid data received.");
                }

                $this->service->updateImage(basename($uri), $data->alt);

                // get image
                $image = $this->service->getImageById(basename($uri));
                echo json_encode($image);
                return;
            }

            $this->sendErrorMessage("Invalid request.");
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to update image.", 500);
        }
    }
}
