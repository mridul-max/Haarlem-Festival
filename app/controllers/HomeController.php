<?php
require_once(__DIR__ . "/../models/Customer.php");

class HomeController
{
    const HOME_PAGE = "/../views/home/index.php";
    const CUSTOMER_ACCOUNT_PAGE = "/../views/account/customerAccount.php";
    const EMPLOYEE_ACCOUNT_PAGE = "/../views/account/employeeAccount.php";
    const LOGIN_PAGE = "/../views/account/login.php";
    const REGISTER_PAGE = "/../views/account/register.php";
    
    //load home 
    public function index(): void
    {
        require(__DIR__ . self::HOME_PAGE);
    }

    public function account(): void
    {
        if (!isset($_SESSION['user'])) {
            require(__DIR__ . self::LOGIN_PAGE);
        } else {
            $user = unserialize($_SESSION['user']);

            if ($user->getUserType() == 3) {
                require(__DIR__ . self::CUSTOMER_ACCOUNT_PAGE);
            } else {
                require(__DIR__ . self::EMPLOYEE_ACCOUNT_PAGE);
            }
        }
    }

    public function register(): void
    {
        require(__DIR__ . self::REGISTER_PAGE);
    }
}
