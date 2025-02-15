<?php
require_once(__DIR__ . '/Repository.php');
require_once(__DIR__ . '/../models/Customer.php');
require_once(__DIR__ . '/../repositories/AddressRepository.php');
require_once(__DIR__ . '/../repositories/UserRepository.php');

class CustomerRepository extends Repository{
    
    public function __construct()
    {
        parent::__construct();
    }

    private function buildCustomer($result) : Customer
    {
        $customer = new Customer();
        $customer->setUserId($result['userId']);
        $customer->setEmail($result['email']);
        $customer->setHashPassword($result['hashPassword']);
        $customer->setFirstName($result['firstName']);
        $customer->setLastName($result['lastName']);
        $customer->setUserType(3);
        $customer->setRegistrationDate(new DateTime($result['registrationDate']));
        $customer->setDateOfBirth(new DateTime($result['dateOfBirth']));
        $customer->setPhoneNumber($result['phoneNumber']);
        $customer->setAddress(new Address());
        $customer->getAddress()->setAddressId($result['addressId']);
        $customer->getAddress()->setStreetName($result['streetName']);
        $customer->getAddress()->setHouseNumber($result['houseNumber']);
        $customer->getAddress()->setPostalCode($result['postalCode']);
        $customer->getAddress()->setCity($result['city']);
        $customer->getAddress()->setCountry($result['country']);

        return $customer;
    }

    public function getById($id) : Customer
    {
        try{
            $query = "SELECT *
                        from customers c
                        join users u on u.userId = c.userId 
                        join addresses a on a.addressId = c.addressId
                        where c.userId = :userId";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(":userId", htmlspecialchars($id));
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result){
                require_once(__DIR__ . '/../models/Exceptions/UserNotFoundException.php');
                throw new UserNotFoundException();
            }

            //Build customer from the result and return it
            return $this->buildCustomer($result);

        }
        catch(Exception $ex)
        {
            throw ($ex);
        }        
    }
    
    public function insertCustomer($customer) : void
    {
        $query = "INSERT INTO customers (dateOfBirth, phoneNumber, addressId, userId) " .
                            "VALUES (:dateOfBirth, :phoneNumber, :addressId, :userId)";
        $stmt = $this->connection->prepare($query);
        
        $stmt->bindValue(":dateOfBirth", htmlspecialchars($customer->getDateOfBirthAsString()));
        $stmt->bindValue(":phoneNumber", htmlspecialchars($customer->getPhoneNumber()));
        $stmt->bindValue(":addressId", htmlspecialchars($customer->getAddress()->getAddressId()));
        $stmt->bindValue(":userId", htmlspecialchars($customer->getUserId()));
        
        $stmt->execute();
        
    }

    public function updateCustomer(Customer $customer) : void
    {
        $query =    "UPDATE customers SET dateOfBirth = :dateOfBirth, phoneNumber = :phoneNumber, addressId = :addressId " .
                    "WHERE userId = :userId";
        $stmt = $this->connection->prepare($query);
        
        $stmt->bindValue(":dateOfBirth", htmlspecialchars($customer->getDateOfBirthAsString()));
        $stmt->bindValue(":phoneNumber", htmlspecialchars($customer->getPhoneNumber()));
        $stmt->bindValue(":addressId", htmlspecialchars($customer->getAddress()->getAddressId()));
        $stmt->bindValue(":userId", htmlspecialchars($customer->getUserId()));
        
        $stmt->execute();
    }
       
    
}
?>