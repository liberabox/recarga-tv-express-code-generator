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
            ->searchMailbox('FROM "info@mercadopago.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN');

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
        $domDocument = new \DOMDocument();
        libxml_use_internal_errors(true);
        $domDocument->loadHTML($mail->textHtml);
        $xPath = new \DOMXPath($domDocument);
        $email = $xPath
            ->query('/html/body/table[3]/tr/td/div[2]/p[3]')
            ->item(0)
            ->textContent;
        $product = str_replace('Você recebeu um pagamento por TV express', '', $mail->subject);

        return new Sale(new Email($email), $product);
    }
}
