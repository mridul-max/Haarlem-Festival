<?php
class AuthenticationException extends Exception
{
    public function __construct($message = "Authentication failed.", $code = 401){
        parent::__construct($message, $code);
    }
}