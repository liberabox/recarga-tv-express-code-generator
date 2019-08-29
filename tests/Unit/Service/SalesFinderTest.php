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
    private $searchCriteria;

    protected function setUp(): void
    {
        $mailboxMock = $this->createMock(Mailbox::class);

        $this->mailbox = $mailboxMock;
        $this->searchCriteria = 'FROM "info@mercadopago.com" SUBJECT "recebeu um pagamento por TV express" UNSEEN';
    }

    public function testShouldReturnEmptyArrayIfThereAreNoEmails()
    {
        $this->mailbox->expects($this->once())
            ->method('searchMailbox')
            ->with($this->searchCriteria)
            ->willReturn([]);

        $salesFinder = new SalesFinder($this->mailbox);
        $sales = $salesFinder->findSales();

        $this->assertCount(0, $sales);
    }

    /**
     * @dataProvider emails
     * @param string $emailBody
     */
    public function testShouldReturnASalesArrayOnSuccessCase(string $emailBody)
    {
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
            ->with($this->searchCriteria)
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
        $this->assertSame($sales[0]->product, $sales[1]->product);
        $this->assertSame('anual', $sales[0]->product);
        $this->assertEquals($sales[0]->costumerEmail, $sales[1]->costumerEmail);
        $this->assertEquals('email@example.com', $sales[0]->costumerEmail);
    }

    public function emails(): array
    {
        $dataDir = __DIR__ . '/../../data';
        return [
            'Without phone' => [file_get_contents("$dataDir/email-without-phone.html")],
            'With phone' => [file_get_contents("$dataDir/email-without-phone.html")],
            'Without name' => [file_get_contents("$dataDir/email-without-name.html")],
            'With two credit cards' => [file_get_contents("$dataDir/email-with-two-credit-cards.html")],
        ];
    }
}
