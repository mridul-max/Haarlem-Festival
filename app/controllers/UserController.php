<?php
require_once("../services/UserService.php");
/**
 * @author Vedat
 */
class UserController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function manageUsers()
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user'])) {
                header("Location: /");
            }

            $user = unserialize($_SESSION['user']);
            if ($user->getUserTypeAsString() != "Admin") {
                header("Location: /");
            }
            
            $users = $this->userService->getAllUsers();
            require("../views/admin/User management/manageUsers.php");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function updateUser()
    {
        try {
            $user = $this->userService->getUserById($_GET['id']);
            require("../views/admin/User management/updateUser.php");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addUser()
    {
        try {
            require("../views/admin/addUser.php");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
