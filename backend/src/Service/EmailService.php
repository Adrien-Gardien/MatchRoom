<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail($userEmail, $verificationCode)
    {
        $email = (new Email())
            ->from('support@matchroom.io')
            ->to($userEmail)
            ->subject('Your Verification Code')
            ->html('<p>Your verification code is: <strong>' . $verificationCode . '</strong></p>Enter it to verify your email address at this link : <a href="http://localhost/email">Vérifier mon email</a></p>');
    
        $this->mailer->send($email);
    }
}
