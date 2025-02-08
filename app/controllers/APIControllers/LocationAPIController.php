<?php

require_once(__DIR__ . "/../../services/LocationService.php");
require_once('APIController.php');
require_once(__DIR__ . "/../../models/Exceptions/MissingVariableException.php");

class LocationAPIController extends APIController
{
    private $locationService;

    // A list of all required variables for creating a location
    private $requiredVariables = [
        'name' => "Name",
        'locationType' => "Location type",
        'lon' => "Longitude",
        'lat' => "Latitude",
        'capacity' => "Capacity",
        'address' => [
            'streetName' => "Street name",
            'houseNumber' => "House number",
            'postalCode' => "Postal code",
            'city' => "City",
            'country' => "Country"
        ]
    ];

    public function __construct()
    {
        $this->locationService = new LocationService();
    }

    public function handleGetRequest($uri)
    {
        try {
            if (str_starts_with($uri, "/api/locations/geocode")) {
                // Request the geocode of a location
                $this->getGeocode();
                return;
            } elseif (str_starts_with($uri, "/api/locations/types")) {
                // Request the all available location types
                $this->getLocationTypes();
                return;
            }

            $sort = isset($_GET['sort']) ? $_GET['sort'] : null;

            if (str_starts_with($uri, "/api/locations/type/")) {
                $this->getLocationsByType($uri, $sort);
                return;
            }

            // Request a specific location by its id
            if (is_numeric(basename($uri))) {
                echo json_encode($this->locationService->getById(basename($uri)));
                return;
            }

            // Request all locations
            echo json_encode($this->locationService->getAll($sort));
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrive locations.", 500);
        }
    }

    private function getGeocode()
    {
        if (!isset($_GET['street'])) {
            $this->sendErrorMessage("Street is required", 400);
            return;
        }
        if (!isset($_GET['number'])) {
            $this->sendErrorMessage("House number is required", 400);
            return;
        }
        if (!isset($_GET['postal'])) {
            $this->sendErrorMessage("Postal code is required", 400);
            return;
        }
        if (!isset($_GET['city'])) {
            $this->sendErrorMessage("City is required", 400);
            return;
        }

        $street = $_GET['street'];
        $houseNumber = $_GET['number'];
        $postalCode = $_GET['postal'];
        $city = $_GET['city'];

        $output = $this->locationService->fetchGeocoding($street, $houseNumber, $postalCode, $city);
        echo json_encode($output);
    }

    private function getLocationsByType($uri, $sort)
    {
        $base = basename($uri);
        // remove stuff after "?"
        $base = explode("?", $base)[0];
        echo json_encode($this->locationService->getLocationsByType($base, $sort));
    }

    public function handlePostRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if ($data == null) {
            $this->sendErrorMessage("Invalid JSON", 400);
            return;
        }

        try {
            foreach ($this->requiredVariables as $key => $value) {
                // Check if $data has the required key
                if (!array_key_exists($key, $data) || $data[$key] == null || $data[$key] == "") {
                    throw new MissingVariableException($value . " is required");
                }

                // If it is, set the value to the variable
                $$key = $data[$key];
            }


            $location = $this->locationService->insertLocation(
                $name,
                $address['streetName'],
                $address['houseNumber'],
                $address['postalCode'],
                $address['city'],
                $address['country'],
                $locationType,
                $lon,
                $lat,
                $capacity
            );

            echo json_encode($location);
        } catch (MissingVariableException $e) {
            Logger::write($e);
            $this->sendErrorMessage("Could not post new location: " . $e->getMessage(), 400);
        }
    }

    public function handlePutRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        if (!is_numeric(basename($uri))) {
            $this->sendErrorMessage("Invalid ID", 400);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if ($data == null) {
            $this->sendErrorMessage("Invalid JSON", 400);
            return;
        }

        try {
            foreach ($this->requiredVariables as $key => $value) {
                // Check if $data has the required key
                if (!array_key_exists($key, $data) || $data[$key] == null || $data[$key] == "") {
                    throw new MissingVariableException($value . " is required");
                }

                // If it is, set the value to the variable
                $$key = $data[$key];
            }

            // Posting also should have an addressId.
            if (!isset($data['address']['addressId'])) {
                throw new MissingVariableException("Address ID is required");
            }


            $addressId = $data['address']['addressId'];

            $location = $this->locationService->updateLocation(
                basename($uri),
                $name,
                $address['streetName'],
                $address['houseNumber'],
                $address['postalCode'],
                $address['city'],
                $address['country'],
                $locationType,
                $lon,
                $lat,
                $capacity,
                $addressId
            );

            echo json_encode($location);
        } catch (MissingVariableException $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to edit location.", 400);
        }
    }

    public function handleDeleteRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        if (!is_numeric(basename($uri))) {
            $this->sendErrorMessage("Invalid ID", 400);
            return;
        }

        try {
            $this->locationService->deleteLocation(basename($uri));
            $this->sendSuccessMessage("Location deleted");
        } catch (Exception $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to delete location.", 400);
        }
    }

    private function getLocationTypes()
    {
        $locationTypes = Location::$LOCATION_TYPE_NAMES;
        // split the key and value
        $locationTypes = array_map(function ($key, $value) {
            return [
                "id" => $key,
                "name" => $value
            ];
        }, array_keys($locationTypes), $locationTypes);

        echo json_encode($locationTypes);
    }
}
