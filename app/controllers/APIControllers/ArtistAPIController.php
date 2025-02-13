<?php
require_once("APIController.php");
require_once(__DIR__ . "/../../services/ArtistService.php");
require_once(__DIR__ . "/../../models/Exceptions/MissingVariableException.php");

class ArtistAPIController extends APIController
{
    private $service;

    public function __construct()
    {
        $this->service = new ArtistService();
    }

    public function handleGetRequest($uri)
    {
        try {
            if (basename($uri) == "kinds") {
                echo json_encode($this->service->getArtistKinds());
                return;
            }

            if (is_numeric(basename($uri))) {
                echo json_encode($this->service->getById(basename($uri)));
                return;
            }

            $sort = $_GET["sort"] ?? "name";
            $filters = [];
            if (isset($_GET["kind"])) {
                $filters["kind"] = $_GET["kind"];
            }

            echo json_encode($this->service->getAll($sort, $filters));
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrieve artists.", 500);
        }
    }

    private function getIfSet($data, $key)
    {
        if (isset($data->$key)) {
            return $data->$key;
        }

        return "";
    }
}
