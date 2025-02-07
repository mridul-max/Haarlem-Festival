<?php

/**
 * Thrown when requested addressId was not found in the database.
 * @author Joshua
 */
class AddressNotFoundException extends Exception
{
    public function __construct(){
        parent::__construct("Address not found in the database.", 404);
    }
}
