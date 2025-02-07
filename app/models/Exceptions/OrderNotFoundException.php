<?php

/**
 * Thrown when requested order was not found in the database.
 * @author Joshua
 */
class OrderNotFoundException extends Exception
{
    public function __construct(){
        parent::__construct("Order not found in the database.", 404);
    }
}
