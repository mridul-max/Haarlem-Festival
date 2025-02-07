<?php
require_once(__DIR__ . "/../models/Address.php");
require_once(__DIR__ . "/../repositories/AddressRepository.php");

class AddressService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new AddressRepository();
    }

    public function getAddressById($id): Address
    {
        return $this->repo->getAddressById($id);
    }

    public function insertAddress($address): Address
    {
        return $this->repo->insertAddress($address);
    }

    public function updateAddress($id, $address): Address
    {
        return $this->repo->updateAddress($id, $address);
    }

    public function deleteAddress($addressId): void
    {
        $this->repo->deleteAddress($addressId);
    }

    public function fetchAddressFromPostCodeAPI($data): mixed
    {
        // Create a stream
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Bearer 1b9faa1d-1521-43ca-af73-4caeb208222b"
            )
        );

        $context = stream_context_create($opts);

        $url = "https://postcode.tech/api/v1/postcode?postcode=" . $data['postalCode'] . "&number=" . $data['houseNumber'];
        // Open the file using the HTTP headers set above
        $response = file_get_contents($url, false, $context);

        $address = json_decode($response);

        if (isset($address->message)) {
            throw new Exception("Something went wrong while fetching the address: " . $address->message);
        }

        return $address;
    }
}
