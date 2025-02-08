<?php

class IncorrectPasswordException extends Exception
{
    public function __construct(){
        parent::__construct("Incorrect password.", 400);
    }
}
