<?php

namespace CViniciusSDias\RecargaTvExpress\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use PhpImap\Mailbox;

class SalesFinder
{
    private Mailbox $mailbox;
    /** @var int[] */
    private array $mailIds;
    private EmailParser $emailParser;

    public function __construct(Mailbox $mailbox, EmailParser $emailParser)
    {
        $this->mailbox = $mailbox;
        $this->mailIds = [];
        $this->emailParser = $emailParser;
    }

    /** @return Sale[] */
    public function findSales(): array
    {
        $this->mailIds = $this->mailbox
            ->searchMailbox('UNSEEN');

        if (empty($this->mailIds)) {
            return [];
        }

        $sales = [];
        foreach ($this->mailIds as $mailId) {
            $mail = $this->mailbox->getMail($mailId);

            $sales[] = $this->emailParser->parse($mail);
        }

        return array_filter($sales);
    }
}
