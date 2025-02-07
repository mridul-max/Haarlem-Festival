<?php
require_once("../services/TicketService.php");

class TicketController
{
    private $ticketService;

    public function __construct()
    {
        $this->ticketService = new TicketService();
    }

    public function markTicketAsScanned()
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user'])) {
                header("Location: /");
            }

            $user = unserialize($_SESSION['user']);
            if ($user->getUserTypeAsString() != "Employee") {
                header("Location: /");
                return;
            }

            if (isset($_SESSION['user'])) {
                $ticketId = $_GET['ticketId'];
                $ticket = $this->ticketService->getTicketByID($ticketId);

                $this->ticketService->markTicketAsScanned($ticket);
                require_once("../views/employee/ticketScan.php");
                return $ticket;
            } else {
                header("Location: /");
            }
        } catch (Exception $ex) {
            throw ($ex);
        }
    }
}
