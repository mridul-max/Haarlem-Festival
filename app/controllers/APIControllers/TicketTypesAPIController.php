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

    public function handlePostRequest($uri)
    {
        if (!$this->isLoggedInAsAdmin()) {
            $this->sendErrorMessage('You are not logged in as admin.', 401);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $ticketType = new TicketType(0, $data['name'], $data['price'], $data['nrOfPeople']);
            $ticketType = $this->ttService->create($ticketType);
            echo json_encode($ticketType);
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to create ticket type.", 500);
        }
    }

    public function handlePutRequest($uri)
    {
        try {
            if (!$this->isLoggedInAsAdmin()) {
                $this->sendErrorMessage('You are not logged in as admin.', 401);
                return;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $ticketType = new TicketType(basename($uri), $data['name'], $data['price'], $data['nrOfPeople']);
            $ticketType = $this->ttService->update($ticketType);
            echo json_encode($ticketType);
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to update ticket type.", 500);
        }
    }

    public function handleDeleteRequest($uri)
    {
        try {
            if (!$this->isLoggedInAsAdmin()) {
                $this->sendErrorMessage('You are not logged in as admin.', 401);
                return;
            }
            $this->ttService->delete(basename($uri));
            $this->sendSuccessMessage('Ticket Type Removed.');
        } catch (Throwable $e) {
            Logger::write($e);
            $this->sendErrorMessage("Unable to delete ticket type.", 500);
        }
    }
}
