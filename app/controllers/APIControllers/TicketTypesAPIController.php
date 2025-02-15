<?php

require_once('APIController.php');
require_once(__DIR__ . '/../../services/TicketTypeService.php');

class TicketTypesAPIController extends APIController
{
    private $ttService;

    public function __construct()
    {
        $this->ttService = new TicketTypeService();
    }

    public function handleGetRequest($uri)
    {
        try {
            if (is_numeric(basename($uri))) {
                $id = basename($uri);
                $ticketType = $this->ttService->getById($id);
                echo json_encode($ticketType);
            } else {
                $ticketTypes = $this->ttService->getAll();
                echo json_encode($ticketTypes);
            }
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to retrive ticket types.", 500);
        }
    }

}