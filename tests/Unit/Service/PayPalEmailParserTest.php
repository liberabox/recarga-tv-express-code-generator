<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Unit\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Service\PayPalEmailParser;
use PhpImap\IncomingMail;
use PHPUnit\Framework\TestCase;

class PayPalEmailParserTest extends TestCase
{
    public function testShouldReturnASalesArrayOnSuccessCase()
    {
        // arrange
        $emailBody = file_get_contents(__DIR__ . '/../../data/email-with-payment-from-paypal.html');
        $parser = new PayPalEmailParser();

        $incomingMailMock = $this->createStub(IncomingMail::class);
        $incomingMailMock->fromAddress = 'service@paypal.com.br';
        $incomingMailMock->method('__get')
            ->willReturn($emailBody);

        // act
        $sale = $parser->parse($incomingMailMock);

        // assert
        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertSame('anual', $sale->product);
        $this->assertEquals('email@example.com', $sale->costumerEmail);
    }

    public function testShouldRaiseErrorWhenTryingToParseUnsupportedEmail()
    {
        $this->expectException(\Error::class);

        // arrange
        $parser = new PayPalEmailParser();
        $incomingMailMock = $this->createStub(IncomingMail::class);
        $incomingMailMock->fromAddress = 'info@mercadopago.com';

        // act
        $parser->parse($incomingMailMock);
    }
}
