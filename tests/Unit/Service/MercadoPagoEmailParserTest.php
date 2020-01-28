<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Unit\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Service\MercadoPagoEmailParser;
use PhpImap\IncomingMail;
use PHPUnit\Framework\TestCase;

/**
 * @todo Create tests to make sure invalid products aren't parsed
 */
class MercadoPagoEmailParserTest extends TestCase
{
    /**
     * @dataProvider emails
     * @param string $emailBody
     */
    public function testShouldParseCorrectlyASaleFromMercadoPagoEmail(string $emailBody)
    {
        // arrange
        $parser = new MercadoPagoEmailParser();

        $incomingMailMock = $this->createStub(IncomingMail::class);
        $incomingMailMock->subject = ' Você recebeu um pagamento por P 1     ';
        $incomingMailMock->fromAddress = 'info@mercadopago.com';
        $incomingMailMock->method('__get')
            ->willReturn($emailBody);

        $incomingMailMock2 = clone $incomingMailMock;
        $incomingMailMock2->subject = ' Você recebeu um pagamento por P 2     ';

        // act
        $sale1 = $parser->parse($incomingMailMock);
        $sale2 = $parser->parse($incomingMailMock2);

        // assert
        $this->assertInstanceOf(Sale::class, $sale1);
        $this->assertSame('mensal', $sale1->product);
        $this->assertSame('anual', $sale2->product);
        $this->assertEquals('email@example.com', $sale1->costumerEmail);
    }

    public function testShouldRaiseErrorWhenTryingToParseUnsupportedEmail()
    {
        $this->expectException(\Error::class);

        // arrange
        $parser = new MercadoPagoEmailParser();
        $incomingMailMock = $this->createStub(IncomingMail::class);
        $incomingMailMock->subject = 'Você recebeu um pagamento por Combo MFC + TVE anual';
        $incomingMailMock->fromAddress = 'info@mercadopago.com';
        $incomingMailMock->method('__get')
            ->willReturn('');

        // act
        $parser->parse($incomingMailMock);
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
