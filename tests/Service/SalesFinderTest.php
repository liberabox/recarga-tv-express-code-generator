<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Service\SalesFinder;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use PHPUnit\Framework\TestCase;

class SalesFinderTest extends TestCase
{
    private $mailbox;

    protected function setUp(): void
    {
        $mailboxMock = $this->getMockBuilder(Mailbox::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailbox = $mailboxMock;
    }

    public function testShouldReturnEmptyArrayIfThereAreNoEmails()
    {
        $this->mailbox->expects($this->once())
            ->method('searchMailbox')
            ->with('FROM "info@mercadopago.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN')
            ->willReturn([]);

        $salesFinder = new SalesFinder($this->mailbox);
        $sales = $salesFinder->findSales();

        $this->assertCount(0, $sales);
    }

    public function testShouldReturnOneSaleWhenAnEmailWithoutPhoneIsFound()
    {
        $emailBody = file_get_contents(__DIR__ . '/../data/email-without-phone.html');
        $emailSubject = 'Você recebeu um pagamento por TV express anual';

        $incomingMailMock = $this->getMockBuilder(IncomingMail::class)
            ->getMock();
        $incomingMailMock->subject = $emailSubject;
        $incomingMailMock->expects($this->exactly(2))
            ->method('__get')
            ->with($this->equalTo('textHtml'))
            ->willReturn($emailBody);
        $this->mailbox->expects($this->once())
            ->method('searchMailbox')
            ->with('FROM "info@mercadopago.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN')
            ->willReturn([1, 2]);
        $this->mailbox->expects($this->exactly(2))
            ->method('getMail')
            ->withConsecutive(
                [$this->equalTo(1)],
                [$this->equalTo(2)],
            )
            ->willReturn($incomingMailMock);

        $salesFinder = new SalesFinder($this->mailbox);
        $sales = $salesFinder->findSales();

        $this->assertCount(2, $sales);
        $this->assertContainsOnlyInstancesOf(Sale::class, $sales);
        $this->assertTrue($sales[0]->product === $sales[1]->product);
        $this->assertEquals('anual', $sales[0]->product);
        $this->assertTrue($sales[0]->costumerEmail == $sales[1]->costumerEmail);
        $this->assertEquals('email@test.com', $sales[0]->costumerEmail);
    }

    public function testShouldReturnOneSaleWhenAnEmailWithPhoneIsFound()
    {
        $emailBody = file_get_contents(__DIR__ . '/../data/email-with-phone.html');
        $emailSubject = 'Você recebeu um pagamento por TV express anual';

        $incomingMailMock = $this->getMockBuilder(IncomingMail::class)
            ->getMock();
        $incomingMailMock->subject = $emailSubject;
        $incomingMailMock->expects($this->exactly(2))
            ->method('__get')
            ->with($this->equalTo('textHtml'))
            ->willReturn($emailBody);
        $this->mailbox->expects($this->once())
            ->method('searchMailbox')
            ->with('FROM "info@mercadopago.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN')
            ->willReturn([1, 2]);
        $this->mailbox->expects($this->exactly(2))
            ->method('getMail')
            ->withConsecutive(
                [$this->equalTo(1)],
                [$this->equalTo(2)],
                )
            ->willReturn($incomingMailMock);

        $salesFinder = new SalesFinder($this->mailbox);
        $sales = $salesFinder->findSales();

        $this->assertCount(2, $sales);
        $this->assertContainsOnlyInstancesOf(Sale::class, $sales);
        $this->assertTrue($sales[0]->product === $sales[1]->product);
        $this->assertEquals('anual', $sales[0]->product);
        $this->assertTrue($sales[0]->costumerEmail == $sales[1]->costumerEmail);
        $this->assertEquals('email@test.com', $sales[0]->costumerEmail);
    }
}
