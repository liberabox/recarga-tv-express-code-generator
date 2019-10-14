<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Unit\Model\VO;

use CViniciusSDias\RecargaTvExpress\Model\VO\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testCreatingWithInvalidEmailShouldThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid-email não é um endereço de e-mail válido');
        new Email('invalid-email');
    }

    public function testCastingToStringShouldReturnTheAddress()
    {
        $email = new Email('email@test.com');
        $this->assertEquals('email@test.com', (string) $email);
    }
}
