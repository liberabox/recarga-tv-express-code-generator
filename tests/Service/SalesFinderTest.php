<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Service\SalesFinder;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use PHPUnit\Framework\TestCase;

class SalesFinderTest extends TestCase
{
    public function testShouldReturnEmptyArrayIfThereAreNoEmails()
    {
        $mailboxMock = $this->getMockBuilder(Mailbox::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mailboxMock->expects($this->once())
            ->method('searchMailbox')
            ->with('FROM "wolneidias@gmail.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN')
            ->willReturn([]);
        $salesFinder = new SalesFinder($mailboxMock);
        $sales = $salesFinder->findSales();

        $this->assertCount(0, $sales);
    }

    public function testShouldReturnSalesArrayInCaseOfSuccess()
    {
        $emailBody = <<<EMAIL
        Express,
        Foi creditado um pagamento na sua conta do Mercado Pago.
        
        Pagamento: R$ 000,00
        
        Tarifa do Mercado Pago: R$ -0,00
        
        Total creditado na sua conta: R$ 000,00
        
        Número da operação: 0000000000
        
        Contraparte:
        
        Client Name
        
        (19) 3589-1868
        
        email@test.com 
        
        Mercado Pago
        EMAIL;
        $emailSubject = 'Você recebeu um pagamento por TV express ';

        $incomingMailMock = $this->getMockBuilder(IncomingMail::class)
            ->getMock();
        $incomingMailMock->subject = $emailSubject . 'anual';
        $incomingMailMock->expects($this->exactly(2))
            ->method('__get')
            ->with($this->equalTo('textPlain'))
            ->willReturn($emailBody);
        $mailboxMock = $this->getMockBuilder(Mailbox::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mailboxMock->expects($this->once())
            ->method('searchMailbox')
            ->with('FROM "wolneidias@gmail.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN')
            ->willReturn([1, 2]);
        $mailboxMock->expects($this->exactly(2))
            ->method('getMail')
            ->withConsecutive(
                [$this->equalTo(1)],
                [$this->equalTo(2)],
            )
            ->willReturn($incomingMailMock);

        $salesFinder = new SalesFinder($mailboxMock);
        $sales = $salesFinder->findSales();

        $this->assertCount(2, $sales);
        $this->assertContainsOnlyInstancesOf(Sale::class, $sales);
        $this->assertTrue($sales[0]->product === $sales[1]->product);
        $this->assertEquals('anual', $sales[0]->product);
        $this->assertTrue($sales[0]->costumerEmail == $sales[1]->costumerEmail);
        $this->assertEquals('email@test.com', $sales[0]->costumerEmail);
    }
}
