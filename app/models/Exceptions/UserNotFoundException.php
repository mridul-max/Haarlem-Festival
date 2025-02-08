<?php

class UserNotFoundException extends Exception{

    public function __construct(){
        parent::__construct("User not found in the database.", 404);
    }
}
