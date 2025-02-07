<?php
require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../repositories/Repository.php');
require_once(__DIR__ . '/../models/Exceptions/UserNotFoundException.php');

class UserRepository extends Repository
{
    public function getById($userId): User
    {
        try {
            $query = "SELECT * FROM users WHERE userId = :userId";
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(":userId", $userId);
            $stmt->execute();

            $result = $stmt->fetch();

            if (!$result)
                throw new UserNotFoundException();
                
            return $this->buildUser($result);
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function getByEmail($email): User
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(":email", htmlspecialchars($email));
            $stmt->execute();

            $result = $stmt->fetch();

            if (!$result)
                throw new UserNotFoundException();

            return $this->buildUser($result);
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function insertUser($user): User
    {
        try {
            $query = "INSERT INTO users (email, firstName, lastName, hashPassword, userType, registrationDate) VALUES (:email, :firstName, :lastName, :hashPassword, :userType, NOW())";
            $stmt = $this->connection->prepare($query);

            $stmt->bindValue(":email", htmlspecialchars($user->getEmail()));
            $stmt->bindValue(":firstName", htmlspecialchars($user->getFirstName()));
            $stmt->bindValue(":lastName", htmlspecialchars($user->getLastName()));
            $stmt->bindValue(":hashPassword", htmlspecialchars($user->getHashPassword()));
            $stmt->bindValue(":userType", htmlspecialchars($user->getUserType()));

            $stmt->execute();

            $user->setUserId($this->connection->lastInsertId());
            $user->setRegistrationDate(new DateTime());

            return $user;
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    // store reset token in database
    public function storeResetToken($email, $reset_token)
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO resettokens (email, reset_token, sendTime) VALUES (:email, :reset_token, NOW())");
            $stmt->bindValue(":email", htmlspecialchars($email));
            $stmt->bindValue(":reset_token", htmlspecialchars($reset_token));
            $stmt->execute();
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function updatePassword(User $user): void
    {
        try {
            $stmt = $this->connection->prepare("UPDATE users SET hashPassword = :hashPassword WHERE email = :email");
            $data = [
                ':email' => htmlspecialchars($user->getEmail()),
                ':hashPassword' => $user->getHashPassword()
            ];
            $stmt->execute($data);
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function verifyResetToken($email, $reset_token)
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM resettokens WHERE email =:email AND reset_token =:reset_token AND sendTime >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
            $data = [
                ':email' => htmlspecialchars($email),
                ':reset_token' => htmlspecialchars($reset_token)
            ];
            $stmt->execute($data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($result)) {
                return null;
            } else {
                return $result;
            }
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    // get all users from database
    public function getAllUsers(): array
    {
        try {
            $query = "SELECT * FROM users";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll();

            if (is_bool($result))
                throw new UserNotFoundException();

            $users = array();

            foreach ($result as $row) {
                $user = $this->buildUser($row);
                $users[] = $user;
            }

            return $users;
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    private function buildUser($row): User
    {
        $user = new User();
        $user->setUserId($row['userId']);
        $user->setEmail($row['email']);
        $user->setFirstName($row['firstName']);
        $user->setLastName($row['lastName']);
        $user->setHashPassword($row['hashPassword']);
        $user->setUserType($row['userType']);
        $user->setRegistrationDate(new DateTime($row['registrationDate']));

        return $user;
    }

    // add new user or admin to database
    public function addUser(User $user)
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO users (email, firstName, lastName, hashPassword, userType, registrationDate) VALUES (:email, :firstName, :lastName, :hashPassword, :userType, NOW())");
            $data = [
                ':email' => $user->getEmail(),
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':hashPassword' => $user->getHashPassword(),
                ':userType' => $user->getUserType()
            ];
            $stmt->execute($data);
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function deleteUser($id)
    {
        try {
            if($this->userHasOrders($id))
                throw new Exception("User has paid orders.");

            $stmt = $this->connection->prepare("DELETE FROM users WHERE userId = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function updateUser(User $user)
    {
        try {
            $stmt = $this->connection->prepare("UPDATE users SET firstName = :firstName, lastName = :lastName, email = :email, userType = :userType, hashPassword = :hashPassword WHERE userId = :id");
            $data = [
                ':id' => $user->getUserId(),
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $user->getEmail(),
                ':userType' => $user->getUserType(),
                ':hashPassword' => $user->getHashPassword()
            ];
            $stmt->execute($data);
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    public function emailAlreadyExists($email)
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindValue(':email', htmlspecialchars($email));
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($result)) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            throw ($ex);
        }
    }

    private function userHasOrders(int $userId) : bool {

        $sql = "SELECT * 
                FROM orders 
                WHERE customerId = :userId AND isPaid = 1";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":userId", $userId);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return !(!$result);
    }
}