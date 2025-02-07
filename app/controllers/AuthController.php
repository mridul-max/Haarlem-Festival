<?php
/**
 * @author: Vedat
 */
class AuthController
{
    public function provideEmail(): void
    {
        try{
            require_once("../views/auth/provideEmail.php");
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function updatePassword(): void
    {
        try {
            require_once("../views/auth/updatePassword.php");

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}