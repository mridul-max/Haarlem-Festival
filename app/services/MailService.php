<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../models/Customer.php');
require_once(__DIR__ . '/../models/Ticket/Ticket.php');
require_once(__DIR__ . '/../models/Order.php');
require_once(__DIR__ . '/../services/UserService.php');

use Dompdf\Dompdf;


/**
 * Handels all email sending
 * @author: Joshua
 */
class MailService
{

    private $mailer;
    const CUSTOMER_CHANGES_EMAIL = __DIR__ . "/../emails/customer-changes-email.php";
    const TICKET_EMAIL = __DIR__ . "/../emails/ticket-email.php";
    const INVOICE_EMAIL = __DIR__ . "/../emails/invoice-email.php";
    const PASSWORD_RESET_EMAIL = __DIR__ . "/../emails/resetPassword.php";


    function __construct()
    {
        $this->mailer = new PHPMailer();
        $this->mailer->isSMTP();
        $this->mailer->isHTML(true);
        $this->mailer->Host = 'haarlem.kfigura.nl';
        $this->mailer->SMTPAuth = true;
        // SSL/TLS
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Port = 465;
        $this->mailer->Username = "team@haarlem.kfigura.nl";
        $this->mailer->Password = 'teampassword';


        $this->mailer->setFrom('team@haarlem.kfigura.nl', 'The Festival Team');
    }

    public function sendResetTokenToUser($email, $reset_token, $user)
    {
        try {
            $userService = new UserService();
            $user = $userService->getUserByEmail($email);

            $this->mailer->Subject = 'Reset Your Password';

            ob_start();
            require_once(self::PASSWORD_RESET_EMAIL);
            $this->mailer->Body = ob_get_clean();

            $this->mailer->addAddress($email);
            $this->mailer->send();
        } catch (Throwable $ex) {
            Logger::write($ex);
            throw ($ex);
        }
    }

    public function sendInvoiceByEmail(Dompdf $dompdf, Order $order)
    {
        try {
            $pdfContents = $dompdf->output();

            $recipentEmail = $order->getCustomer()->getEmail();
            $name = $order->getCustomer()->getFullName();

            $this->mailer->Subject = 'Your Invoice for the The Festival';

            ob_start();
            require_once(self::INVOICE_EMAIL);
            $this->mailer->Body = ob_get_clean();

            $this->mailer->addAddress($recipentEmail, $name);
            $this->mailer->addStringAttachment($pdfContents, 'invoice.pdf', 'base64', 'application/pdf');

            if (!$this->mailer->send()) {
                throw new Exception("Email with invoice could not be sent");
            }
        } catch (Throwable $ex) {
            Logger::write($ex);
            throw ($ex);
        }
    }

    public function sendTicketByEmail(Dompdf $dompdf, Order $order)
    {
        try {
            $this->mailer->Subject = 'Your Ticket for the The Festival';

            $recipentEmail = $order->getCustomer()->getEmail();
            $name = $order->getCustomer()->getFullName();

            ob_start();
            require_once(self::TICKET_EMAIL);
            $this->mailer->Body = ob_get_clean();

            $this->mailer->addAddress($recipentEmail, $name);
            // add pdf to email for each ticket
            foreach ($order->getTickets() as $ticket) {
                $pdfContents = $dompdf->output();
                $this->mailer->addStringAttachment($pdfContents, 'ticket.pdf', 'base64', 'application/pdf');
            }

            if (!$this->mailer->send()) {
                // Get reason.
                throw new Exception("Email with tickets could not be sent");
            }
        } catch (Throwable $ex) {
            Logger::write($ex);
            throw ($ex);
        }
    }

    public function sendAccountUpdateEmail($customer)
    {
        //Create email by loading customer data into HTML template
        ob_start();
        require_once(self::CUSTOMER_CHANGES_EMAIL);
        $this->mailer->Body = ob_get_clean();

        //Add subject
        $this->mailer->Subject = 'Changes to your account';

        //Add recipient
        $this->mailer->addAddress($customer->getEmail(), $customer->getFullName());

        //Send email, throw exception if something goes wrong.

        if (!$this->mailer->send()) {
            throw new Exception("Email could not be sent!");
        }
    }
}
