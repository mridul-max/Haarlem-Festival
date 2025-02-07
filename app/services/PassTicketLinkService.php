<?php

require_once("TicketLinkService.php");
require_once(__DIR__ . "/../repositories/PassTicketLinkRepository.php");

class PassTicketLinkService extends TicketLinkService
{
    public function __construct()
    {
        parent::__construct();
        $this->repo = new PassTicketLinkRepository();
    }
}
