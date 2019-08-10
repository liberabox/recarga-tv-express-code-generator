<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Model\VO\Email;
use CViniciusSDias\RecargaTvExpress\Repository\CodeRepository;
use CViniciusSDias\RecargaTvExpress\Service\SerialCodeGenerator;
use PHPUnit\Framework\TestCase;

class SerialCodeGeneratorTest extends TestCase
{
    public function testFailureOnAttachingUserMustThrowException()
    {
        $this->expectException(\DomainException::class);

        $pdoMock = $this->getMockBuilder(\PDO::class)->disableOriginalConstructor()->getMock();
        $repositoryMock = $this->getMockBuilder(CodeRepository::class)->disableOriginalConstructor()->getMock();
        $repositoryMock->expects($this->once())
            ->method('attachUserToCode')
            ->willReturn(false);
        $saleMock = $this->getMockBuilder(Sale::class)->disableOriginalConstructor()->getMock();
        $saleMock->method('__get')
            ->with('costumerEmail')
            ->willReturn(new Email('test@email.com'));

        $codeGenerator = new SerialCodeGenerator($pdoMock, $repositoryMock);
        $codeGenerator->generateSerialCode($saleMock);
    }
}
