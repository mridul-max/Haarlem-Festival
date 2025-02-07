<?php

require_once __DIR__ . '/../repositories/TicketRepository.php';
require_once __DIR__ . '/../models/Ticket/Ticket.php';
require_once(__DIR__ . '/../models/TicketLink.php');
require_once(__DIR__ . '/../models/Exceptions/TicketNotFoundException.php');

require_once(__DIR__ . '../../vendor/autoload.php');

use Dompdf\Dompdf;

require_once('../phpmailer/PHPMailer.php');
require_once('../phpmailer/SMTP.php');
require_once('../phpmailer/Exception.php');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Writer\PngWriter;


class TicketService
{
  private TicketRepository $repository;

  public function __construct()
  {
    $this->repository = new TicketRepository();
  }

  public function insertTicket($orderId, OrderItem $orderItem, Event $event, $ticketTypeId): Ticket
  {
    try {
      $ticket = $this->repository->insertTicket($orderId, $orderItem, $event, $ticketTypeId);
      return $ticket;
    } catch (Exception $ex) {
      throw ($ex);
    }
  }

  public function getTicketByID($ticketID): Ticket
  {
    try {
      $ticket = $this->repository->getTicketByID($ticketID);
      return $ticket;
    } catch (Exception $ex) {
      throw ($ex);
    }
  }

  public function getAllHistoryTickets(Order $order): array
  {
    try {
      $eventType = "history";
      $tickets = $this->repository->getAllTicketsByOrderIdAndEventType($order, $eventType);
      return $tickets;
    } catch (Exception $ex) {
      throw ($ex);
    }
  }

  public function getAllJazzTickets(Order $order): array
  {
    try {
      $eventType = "jazz";
      $tickets = $this->repository->getAllTicketsByOrderIdAndEventType($order, $eventType);
      return $tickets;
    } catch (Exception $ex) {
      throw ($ex);
    }
  }

  public function getAllYummyTickets(Order $order): array
  {
    try {
      $tickets = $this->repository->getAllYummyTicketsByOrderId($order);
      return $tickets;
    } catch (Exception $ex) {
      throw ($ex);
    }
  }

  public function getAllPasses(Order $order): array
  {
    try {
      $tickets = $this->repository->getAllDayTicketsForPasses($order);
      return $tickets;
    } catch (Exception $ex) {
      throw ($ex);
    }
  }

}
