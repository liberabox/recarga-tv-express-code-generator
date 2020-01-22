<?php

namespace CViniciusSDias\RecargaTvExpress\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class CodesCountWarningSender
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWarning(int $monthCount, int $yearCount): void
    {
        $emailBody = "Códigos mensais disponíveis: $monthCount\nCódigos anuais disponíveis: $yearCount";


        $email = (new Email())
            ->from('wolneidias@gmail.com')
            ->to('wolneidias@gmail.com')
            ->subject('Códigos acabando!')
            ->priority(5)
            ->text($emailBody);
        $this->mailer->send($email);
    }
}
