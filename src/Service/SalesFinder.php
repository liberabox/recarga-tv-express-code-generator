<?php

namespace CViniciusSDias\RecargaTvExpress\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Model\VO\Email;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;

class SalesFinder
{
    private $mailbox;
    /** @var int[] */
    private $mailIds;

    public function __construct(Mailbox $mailbox)
    {
        $this->mailbox = $mailbox;
        $this->mailIds = [];
    }

    /** @return Sale[] */
    public function findSales(): array
    {
        $this->mailIds = $this->mailbox
            ->searchMailbox('FROM "wolneidias@gmail.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN');

        if (empty($this->mailIds)) {
            return [];
        }

        $sales = [];
        foreach ($this->mailIds as $mailId) {
            $mail = $this->mailbox->getMail($mailId);

            $sales[] = $this->parseEmail($mail);
        }
        return array_filter($sales);
    }

    private function parseEmail(IncomingMail $mail): ?Sale
    {
        $lines = explode("\n", $mail->textPlain);
        foreach ($lines as $line) {
            $email = filter_var(trim($line), FILTER_VALIDATE_EMAIL);

            if ($email !== false) {
                $product = str_replace('Você recebeu um pagamento por TV express', '', $mail->subject);
                return new Sale(new Email($email), $product);
            }
        }
        return null;
    }
}
