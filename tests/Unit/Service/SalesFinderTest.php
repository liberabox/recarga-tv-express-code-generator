<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Service\EmailParser;
use CViniciusSDias\RecargaTvExpress\Service\SalesFinder;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use PHPUnit\Framework\TestCase;

class SalesFinderTest extends TestCase
{
    public function testShouldReturnEmptyArrayIfThereAreNoEmails()
    {
        $mailbox = $this->createStub(Mailbox::class);

        $mailbox->method('searchMailbox')
            ->willReturn([]);

        $nullParser = new class extends EmailParser
        {
            protected function parseEmail(IncomingMail $email): ?Sale
            {
                return null;
            }

            protected function canParse(IncomingMail $email): bool
            {
                return true;
            }
        };
        $salesFinder = new SalesFinder($mailbox, $nullParser);
        $sales = $salesFinder->findSales();

        $this->assertCount(0, $sales);
    }
}
