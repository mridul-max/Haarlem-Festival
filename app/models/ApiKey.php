<?php
class ApiKey
{
    private $id;
    private $token;
    private $name;

    public function __construct($id, $token, $name)
    {
        $this->id = $id;
        $this->token = $token;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getName()
    {
        return $this->name;
    }
}
