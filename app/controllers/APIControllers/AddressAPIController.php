<?php
require_once("APIController.php");
require_once(__DIR__ . "/../../services/AddressService.php");
require_once(__DIR__ . "/../../models/Address.php");
require_once(__DIR__ . "/../../models/Exceptions/MissingVariableException.php");

class AddressAPIController extends APIController
{
    private $addressService;

    public function __construct()
    {
        $this->addressService = new AddressService();
    }

    private function buildAddressFromPostedJson()
    {

        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if ($data == null) {
            $this->sendErrorMessage("Invalid JSON", 400);
            return;
        }

        if (!isset($data->streetName)) {
            throw new MissingVariableException("Street name is required");
        }
        if (!isset($data->houseNumber)) {
            throw new MissingVariableException("House number is required");
        }
        if (!isset($data->postalCode)) {
            throw new MissingVariableException("Postal code is required");
        }
        if (!isset($data->city)) {
            throw new MissingVariableException("City is required");
        }
        if (!isset($data->country)) {
            throw new MissingVariableException("Country is required");
        }

        $address = new Address();
        $address->setStreetName($data->streetName);
        $address->setHouseNumber($data->houseNumber);
        $address->setPostalCode($data->postalCode);
        $address->setCity($data->city);
        $address->setCountry($data->country);

        return $address;
    }

    protected function handleGetRequest($uri)
    {
        if (str_starts_with($uri, "/api/address/fetch-address")) {
            $data = $_GET;
            $this->fetchAddress($data);
            return;
        }

        if (!is_numeric(basename($uri))) {
            $this->sendErrorMessage("Invalid API Request. You can only request specific addresses.", 400);
            return;
        }

        try {
            $addressId = basename($uri);
            echo json_encode($this->addressService->getAddressById($addressId));
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage($e);
        }
    }

    private function fetchAddress($data)
    {
        try {
            $address = $this->addressService->fetchAddressFromPostCodeAPI($data);

            header('Content-Type: application/json');
            echo json_encode([
                "street" => $address->street,
                "city" => $address->city
            ]);
        } catch (Throwable $ex) {
            Logger::write($ex);
            $this->sendErrorMessage("Unable to fetch address.", 400);
        }
    }
}
