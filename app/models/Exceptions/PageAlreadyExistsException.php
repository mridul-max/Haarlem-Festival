<?php
class PageAlreadyExistsException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
