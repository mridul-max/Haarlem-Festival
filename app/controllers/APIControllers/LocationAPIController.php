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
                $this->getGeocode();
                return;
            } elseif (str_starts_with($uri, "/api/locations/types")) {
                $this->getLocationTypes();
                return;
            }

            $sort = isset($_GET['sort']) ? $_GET['sort'] : null;

            if (str_starts_with($uri, "/api/locations/type/")) {
                $this->getLocationsByType($uri, $sort);
                return;
            }
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
        $base = explode("?", $base)[0];
        echo json_encode($this->locationService->getLocationsByType($base, $sort));
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
