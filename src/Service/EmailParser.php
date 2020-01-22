<?php

namespace CViniciusSDias\RecargaTvExpress\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use PhpImap\IncomingMail;

abstract class EmailParser
{
    /** @var EmailParser */
    protected ?EmailParser $next;

    public function __construct(EmailParser $next = null)
    {
        $this->next = $next;
    }

    public function parse(IncomingMail $email): ?Sale
    {
        if (!$this->canParse($email)) {
            return $this->next->parse($email);
        }

        return $this->parseEmail($email);
    }
    abstract protected function parseEmail(IncomingMail $email): ?Sale;
    abstract protected function canParse(IncomingMail $email): bool;
}
