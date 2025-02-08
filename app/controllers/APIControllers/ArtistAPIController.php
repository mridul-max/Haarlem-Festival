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

    public function handlePostRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        $json = file_get_contents('php://input');

        $data = json_decode($json);

        if ($data == null) {
            echo json_encode(["error" => "Invalid JSON"]);
            return;
        }

        try {
            if (!isset($data->name)) {
                throw new MissingVariableException("Name is required");
            }
            $name = $data->name;
            $description = $this->getIfSet($data, "description");
            $recentAlbums = $this->getIfSet($data, "recentAlbums");
            $country = $this->getIfSet($data, "country");
            $genres = $this->getIfSet($data, "genres");
            $homepage = $this->getIfSet($data, "homepage");
            $facebook = $this->getIfSet($data, "facebook");
            $twitter = $this->getIfSet($data, "twitter");
            $instagram = $this->getIfSet($data, "instagram");
            $spotify = $this->getIfSet($data, "spotify");
            $images = $this->getIfSet($data, "images");
            $kindId = $this->getIfSet($data, "kindId");

            echo json_encode($this->service->insertArtist(
                $name,
                $description,
                $recentAlbums,
                $country,
                $genres,
                $homepage,
                $facebook,
                $twitter,
                $instagram,
                $spotify,
                $images,
                $kindId
            ));
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to insert artist.", 500);
        }
    }

    private function getIfSet($data, $key)
    {
        if (isset($data->$key)) {
            return $data->$key;
        }

        return "";
    }

    public function handleDeleteRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        try {
            if (is_numeric(basename($uri))) {
                $this->service->deleteById(basename($uri));
                $this->sendSuccessMessage("Artist deleted");
                return;
            }

            $this->sendErrorMessage("Invalid URI");
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to delete artist.", 500);
        }
    }

    public function handlePutRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        if (!is_numeric(basename($uri))) {
            $this->sendErrorMessage("Invalid URI");
            return;
        }

        $json = file_get_contents('php://input');

        $data = json_decode($json);

        if ($data == null) {
            echo json_encode(["error" => "Invalid JSON"]);
            return;
        }

        try {
            if (!isset($data->name)) {
                throw new MissingVariableException("Name is required");
            }
            $artistId = basename($uri);
            $name = $data->name;
            $description = $this->getIfSet($data, "description");
            $recentAlbums = $this->getIfSet($data, "recentAlbums");
            $country = $this->getIfSet($data, "country");
            $genres = $this->getIfSet($data, "genres");
            $homepage = $this->getIfSet($data, "homepage");
            $facebook = $this->getIfSet($data, "facebook");
            $twitter = $this->getIfSet($data, "twitter");
            $instagram = $this->getIfSet($data, "instagram");
            $spotify = $this->getIfSet($data, "spotify");
            $images = $this->getIfSet($data, "images");
            $kindId = $this->getIfSet($data, "kindId");

            echo json_encode($this->service->updateById(
                $artistId,
                $name,
                $description,
                $recentAlbums,
                $country,
                $genres,
                $homepage,
                $facebook,
                $twitter,
                $instagram,
                $spotify,
                $images,
                $kindId
            ));
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to update artist.", 500);
        }
    }
}
