<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Service;

use CViniciusSDias\RecargaTvExpress\Service\EmailParser;
use CViniciusSDias\RecargaTvExpress\Service\SalesFinder;
use PhpImap\Mailbox;
use PHPUnit\Framework\TestCase;

class SalesFinderTest extends TestCase
{
    public function testShouldReturnEmptyArrayIfThereAreNoEmails()
    {
        $mailbox = $this->createStub(Mailbox::class);

        $mailbox->method('searchMailbox')
            ->willReturn([]);

        $nullParser = $this->createStub(EmailParser::class);
        $salesFinder = new SalesFinder($mailbox, $nullParser);
        $sales = $salesFinder->findSales();

        $this->assertCount(0, $sales);
    }
}
