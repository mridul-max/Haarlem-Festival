<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../models/Customer.php');
require_once(__DIR__ . '/../models/Ticket/Ticket.php');
require_once(__DIR__ . '/../models/Order.php');
require_once(__DIR__ . '/../services/UserService.php');

use Dompdf\Dompdf;

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
        $this->mailer->Host = 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth = true;
        // SSL/TLS
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Port = 2525;
        $this->mailer->Username = "1a5dfcc801fdf2";
        $this->mailer->Password = '6eeb4e609b3156';
   
        $this->mailer->setFrom('mahedimridul57@gmail.com', 'Mahedi');

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
