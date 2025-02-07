<?php

/**
 * Thrown when an email already exists in the database.
 * @author Joshua
 */
class EmailAlreadyExistsException extends Exception
{
    public function __construct(){
        parent::__construct("Email already exists in the database.");
    }
}
?>