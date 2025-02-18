<?php
//
require_once(__DIR__ . '/../models/Address.php');
require_once(__DIR__ . '/../repositories/Repository.php');
require_once(__DIR__ . '/../models/Exceptions/AddressNotFoundException.php');

class AddressRepository extends Repository
{

    public function insertAddress($address): Address
    {
        try {
            $query = "INSERT INTO addresses (streetName, houseNumber, postalCode, city, country) VALUES (:streetName, :houseNumber, :postalCode, :city, :country)";
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(":streetName", htmlspecialchars($address->getStreetName()));
            $stmt->bindValue(":houseNumber", htmlspecialchars($address->getHouseNumber()));
            $stmt->bindValue(":postalCode", htmlspecialchars($address->getPostalCode()));
            $stmt->bindValue(":city", htmlspecialchars($address->getCity()));
            $stmt->bindValue(":country", htmlspecialchars($address->getCountry()));

            $stmt->execute();

            $address->setAddressId($this->connection->lastInsertId());
            return $address;
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function getAddressById($addressId): Address
    {
        try {
            $query = "SELECT * FROM addresses WHERE addressId = :addressId";
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(":addressId", htmlspecialchars($addressId));
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result){
                throw new AddressNotFoundException();
            }
            
            //Build and return Address object
            $address = new Address();
            $address->setAddressId($addressId);
            $address->setStreetName($result['streetName']);
            $address->setHouseNumber($result['houseNumber']);
            $address->setPostalCode($result['postalCode']);
            $address->setCity($result['city']);
            $address->setCountry($result['country']);

            return $address;

        } catch (Exception $ex) {
            throw ($ex);
        }
    }

}
