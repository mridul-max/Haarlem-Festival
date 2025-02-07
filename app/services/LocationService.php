<?php

require_once(__DIR__ . "/../repositories/LocationRepository.php");
require_once(__DIR__ . "/AddressService.php");
require_once(__DIR__ . "/../models/Location.php");
require_once(__DIR__ . '/../models/Exceptions/ObjectNotFoundException.php');

/**
 * @author Konrad
 */
class LocationService
{
    private LocationRepository $repo;
    private $addressService;

    public function __construct()
    {
        $this->repo = new LocationRepository();
        $this->addressService = new AddressService();
    }

    public function getAll($sort = null): array
    {
        $locations = $this->repo->getAll($sort);
        foreach ($locations as $location) {
            $location->setAddress($this->addressService->getAddressById($location->getAddressId()));
        }
        return $locations;
    }

    public function getById($id): Location
    {
        $location = $this->repo->getById($id);
        $location->setAddress($this->addressService->getAddressById($location->getAddressId()));
        return $location;
    }

    /**
     * Retrives locations by different type
     * 1: Jazz & More
     * 2: YUMMY!
     * 3: Stroll
     * 4: DANCE!
     */
    public function getLocationsByType(int $type, $sort = null): array
    {
        $locations = $this->repo->getLocationsByType($type, $sort);
        foreach ($locations as $location) {
            $location->setAddress($this->addressService->getAddressById($location->getAddressId()));
        }
        return $locations;
    }

    public function insertLocation($name, $streetName, $houseNumber, $postalCode, $city, $country, $locationType, $lon, $lat, $capacity): Location
    {
        $address = new Address();
        $address->setStreetName($streetName);
        $address->setHouseNumber($houseNumber);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);
        $address = $this->addressService->insertAddress($address);

        $name = htmlspecialchars($name);
        $locationType = htmlspecialchars($locationType);
        $lon = htmlspecialchars($lon);
        $lat = htmlspecialchars($lat);
        $capacity = htmlspecialchars($capacity);

        $locationId = $this->repo->insertLocation($name, $address->getAddressId(), $locationType, $lon, $lat, $capacity);
        return $this->getById($locationId);
    }

    public function updateLocation($locationId, $name, $streetName, $houseNumber, $postalCode, $city, $country, $locationType, $lon, $lat, $capacity, $addressId): Location
    {
        $locationId = htmlspecialchars($locationId);
        $name = htmlspecialchars($name);
        $locationType = htmlspecialchars($locationType);
        $lon = htmlspecialchars($lon);
        $lat = htmlspecialchars($lat);
        $capacity = htmlspecialchars($capacity);
        $addressId = htmlspecialchars($addressId);

        $address = new Address();
        $address->setStreetName($streetName);
        $address->setHouseNumber($houseNumber);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);

        $address = $this->addressService->updateAddress($addressId, $address);

        $this->repo->updateLocation($locationId, $name, $address->getAddressId(), $locationType, $lon, $lat, $capacity);

        // Jazz Events use the capacity of the location as the "availableTickets". Update that too.
        $this->repo->updateJazzEventCapacity($locationId, $capacity);

        return $this->getById($locationId);
    }

    public function deleteLocation($locationId): void
    {
        $locationId = htmlspecialchars($locationId);
        $this->repo->deleteLocation($locationId);
    }

    const TOMTOM_API_KEY = "hhPEr4bmakfOBlVfPEsMhZWHNlmGt40L";

    /**
     * Fetches geocoding data from TomTom API
     */
    public function fetchGeocoding($street, $buildingNumber, $postal, $city)
    {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Content-Type: application/json"
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );

        $context = stream_context_create($opts);

        $address = urlencode($street . " " . $buildingNumber . ", " . $postal . " " . $city);
        // Oddly enough, TomTom API requires authentication key to be passed as a query parameter, not as a header.
        $url = "https://api.tomtom.com/search/2/geocode/$address.json?key=" . self::TOMTOM_API_KEY;

        $response = file_get_contents($url, true, $context);
        $response = json_decode($response, true);

        // Check if response is null
        if ($response == null) {
            throw new ObjectNotFoundException("Invalid JSON");
        }

        // We're only interested in the first result, and only in the lat/lon data.
        $lat = $response['results'][0]['position']['lat'];
        $lon = $response['results'][0]['position']['lon'];

        return [
            "lat" => $lat,
            "lon" => $lon
        ];
    }
}
